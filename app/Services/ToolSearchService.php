<?php

namespace App\Services;

use App\Models\Tool;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * ToolSearchService — Optimized RAG keyword extraction and full-text search
 * for the chatbot context pipeline.
 *
 * Uses SQLite FTS5 when available (sub-millisecond), falls back to
 * optimized LIKE queries, and includes proper bilingual stop-word filtering.
 */
class ToolSearchService
{
    /**
     * English + Spanish stop words that add no value to tool search.
     */
    protected static array $stopWords = [
        // English
        'a','an','and','as','at','but','by','for','if','in','its','nor','not',
        'of','on','or','so','the','to','up','yet',
        'is','are','was','were','be','been','being',
        'have','has','had','do','does','did','will','would','shall','should',
        'may','might','must','can','could','that','this','these','those',
        'what','which','who','whom','when','where','why','how','with',
        'from','into','about','than','then','some','such','each','every',
        'much','many','more','most','also','just','only','very','really',
        'well','here','there','over','under','after','before','between',
        'through','during','above','below','both','either','neither',
        'other','another','same','help','need','want','like','good',
        'best','please','thank','thanks','hello','hola','know','find',
        'tell','show','give','make','think','look','come','take','keep',
        'recommend','suggest','tool','tools','app','apps','application',
        // Spanish
        'el','la','los','las','un','una','unos','unas','del','al',
        'que','por','para','con','sin','sobre','como','pero','mas','más',
        'este','esta','estos','estas','ese','esa','esos','esas',
        'ser','estar','tener','hacer','poder','querer','deber','saber',
        'haber','hay','muy','bien','cual','cuales','donde','cuando',
        'quien','porque','también','algo','todo','toda','todos','todas',
        'otro','otra','otros','otras','mismo','misma','aquí','allí',
        'ahora','después','antes','entre','desde','hasta','durante',
        'necesito','quiero','puedo','dame','dime','ayuda','mejor',
        'herramienta','herramientas','aplicación','aplicaciones',
    ];

    /**
     * Extract meaningful keywords from a user message.
     *
     * @param  string  $message  Raw user message
     * @return Collection<int, string>
     */
    public static function extractKeywords(string $message): Collection
    {
        // Tokenize: split on whitespace and common punctuation
        $words = preg_split('/[\s,.\?!;:\-\_\(\)\[\]\{\}\"\'\/\\\\]+/u', mb_strtolower($message));

        return collect($words)
            ->filter(fn($w) => mb_strlen($w) > 2)                    // min 3 chars
            ->reject(fn($w) => in_array($w, self::$stopWords, true))  // remove stop words
            ->reject(fn($w) => is_numeric($w))                        // remove pure numbers
            ->unique()
            ->values()
            ->take(8);  // cap at 8 keywords to prevent query explosion
    }

    /**
     * Search tools using FTS5 (fast) or LIKE fallback.
     *
     * @param  Collection<int, string>  $keywords
     * @param  bool  $canSeeDetection  Whether user can see AI Detection tools
     * @param  int  $limit  Max tools to return
     * @return Collection<int, Tool>
     */
    public static function search(Collection $keywords, bool $canSeeDetection = false, int $limit = 5): Collection
    {
        if ($keywords->isEmpty()) {
            return self::trendingFallback($canSeeDetection, $limit);
        }

        $driver = DB::getDriverName();

        // Try FTS5 first (SQLite)
        if ($driver === 'sqlite') {
            $result = self::searchFts5($keywords, $canSeeDetection, $limit);
            if ($result->isNotEmpty()) {
                return $result;
            }
        }

        // Try PostgreSQL full-text search
        if ($driver === 'pgsql') {
            $result = self::searchPgsql($keywords, $canSeeDetection, $limit);
            if ($result->isNotEmpty()) {
                return $result;
            }
        }

        // Fallback: optimized LIKE with single query
        $result = self::searchLike($keywords, $canSeeDetection, $limit);

        return $result->isNotEmpty()
            ? $result
            : self::trendingFallback($canSeeDetection, $limit);
    }

    /**
     * FTS5 search — sub-millisecond, ranked by relevance.
     */
    protected static function searchFts5(Collection $keywords, bool $canSeeDetection, int $limit): Collection
    {
        try {
            // Build FTS5 query: "word1 OR word2 OR word3"
            $ftsQuery = $keywords
                ->map(fn($k) => '"' . str_replace('"', '', $k) . '"')
                ->implode(' OR ');

            // Get matching tool IDs ranked by BM25 relevance
            $matchingIds = DB::select(
                "SELECT rowid, rank FROM tools_fts WHERE tools_fts MATCH ? ORDER BY rank LIMIT ?",
                [$ftsQuery, $limit * 2]  // fetch extra, filter by approval after
            );

            if (empty($matchingIds)) {
                return collect();
            }

            $ids = collect($matchingIds)->pluck('rowid')->toArray();

            return Tool::approved()
                ->with('categoryRelation')
                ->whereIn('id', $ids)
                ->when(! $canSeeDetection, function (Builder $q) {
                    $q->whereHas('categoryRelation', fn($cQ) =>
                        $cQ->where('slug', '!=', 'ai-detection')
                    );
                })
                ->get()
                ->sortBy(fn($tool) => array_search($tool->id, $ids))  // preserve FTS rank
                ->take($limit)
                ->values();
        } catch (\Exception $e) {
            Log::warning('FTS5 search failed, falling back to LIKE', [
                'error' => $e->getMessage(),
            ]);
            return collect();
        }
    }

    /**
     * PostgreSQL full-text search with ts_rank.
     */
    protected static function searchPgsql(Collection $keywords, bool $canSeeDetection, int $limit): Collection
    {
        try {
            $tsQuery = $keywords->implode(' | ');

            return Tool::approved()
                ->with('categoryRelation')
                ->whereRaw("search_vector @@ to_tsquery('english', ?)", [$tsQuery])
                ->when(! $canSeeDetection, function (Builder $q) {
                    $q->whereHas('categoryRelation', fn($cQ) =>
                        $cQ->where('slug', '!=', 'ai-detection')
                    );
                })
                ->orderByRaw("ts_rank(search_vector, to_tsquery('english', ?)) DESC", [$tsQuery])
                ->take($limit)
                ->get();
        } catch (\Exception $e) {
            Log::warning('PostgreSQL FTS failed, falling back to LIKE', [
                'error' => $e->getMessage(),
            ]);
            return collect();
        }
    }

    /**
     * Optimized LIKE fallback — single query with concatenated conditions.
     */
    protected static function searchLike(Collection $keywords, bool $canSeeDetection, int $limit): Collection
    {
        return Tool::approved()
            ->with('categoryRelation')
            ->where(function (Builder $q) use ($keywords) {
                foreach ($keywords as $word) {
                    $escaped = addcslashes($word, '%_');
                    $q->orWhere('name', 'like', "%{$escaped}%")
                      ->orWhere('description', 'like', "%{$escaped}%");
                }
            })
            ->when(! $canSeeDetection, function (Builder $q) {
                $q->whereHas('categoryRelation', fn($cQ) =>
                    $cQ->where('slug', '!=', 'ai-detection')
                );
            })
            ->orderByDesc('click_count')
            ->take($limit)
            ->get();
    }

    /**
     * Fallback: top trending tools.
     */
    protected static function trendingFallback(bool $canSeeDetection, int $limit): Collection
    {
        return Tool::approved()
            ->with('categoryRelation')
            ->when(! $canSeeDetection, function (Builder $q) {
                $q->whereHas('categoryRelation', fn($cQ) =>
                    $cQ->where('slug', '!=', 'ai-detection')
                );
            })
            ->orderByDesc('click_count')
            ->take($limit)
            ->get();
    }
}
