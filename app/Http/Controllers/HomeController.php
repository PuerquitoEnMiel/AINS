<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Tool;
use App\Models\LessonPlan;
use App\Models\PromptTip;
use App\Models\ToolRequest;
use App\Models\BadgeEvidence;
use App\Models\BadgeSuggestion;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class HomeController extends Controller
{
    /**
     * Show the application landing page.
     */
    public function index(\Illuminate\Http\Request $request)
    {
        $canSeeDetection = Auth::check() && (Auth::user()->isTeacher() || Auth::user()->isAdmin());

        // Role-specific data
        $lessonPlanCount = 0;
        $badgeCount = 0;
        $latestBadges = collect();
        $favoriteTools = collect();
        $favoriteToolsCount = 0;
        $recentLessonPlans = collect();
        $studentPrompts = collect();
        $teacherPrompts = collect();

        // Admin approval queue
        $pendingRequests = collect();
        $pendingEvidence = collect();
        $pendingSuggestions = collect();
        $pendingRequestsCount = 0;
        $pendingEvidenceCount = 0;
        $pendingSuggestionsCount = 0;

        if (Auth::check()) {
            $user = Auth::user();
            if ($user->isTeacher()) {
                $lessonPlanCount = LessonPlan::where('user_id', $user->id)->count();
                $badgeCount = $user->badges()->count();
                $latestBadges = $user->badges()->latest('earned_at')->limit(3)->get();
                $favoriteToolsCount = $user->favorites()->count();
                $recentLessonPlans = LessonPlan::where('user_id', $user->id)->latest()->limit(3)->get();
                $teacherPrompts = PromptTip::where('target_role', 'docentes')
                    ->where('is_approved', true)
                    ->orderBy('sort_order')
                    ->latest()
                    ->limit(4)
                    ->get();
            } elseif ($user->isStudent()) {
                $favoriteTools = $user->favorites()->with('categoryRelation')->limit(6)->get();
                $favoriteToolsCount = $user->favorites()->count();
                $studentPrompts = PromptTip::where('target_role', 'estudiantes')
                    ->where('is_approved', true)
                    ->orderBy('sort_order')
                    ->latest()
                    ->limit(4)
                    ->get();
            } elseif ($user->isAdmin()) {
                $pendingRequests = ToolRequest::where('status', 'pending')->latest()->limit(5)->get();
                $pendingEvidence = BadgeEvidence::with(['user', 'badge'])->where('status', 'pending')->latest()->limit(5)->get();
                $pendingSuggestions = BadgeSuggestion::with('user')->where('status', 'pending')->latest()->limit(5)->get();

                $pendingRequestsCount = ToolRequest::where('status', 'pending')->count();
                $pendingEvidenceCount = BadgeEvidence::where('status', 'pending')->count();
                $pendingSuggestionsCount = BadgeSuggestion::where('status', 'pending')->count();
            }
        }

        // Basic query for catalog tools (excluding official tools as they have their own section)
        $query = Tool::approved()
            ->where('is_official', false)
            ->with(['categoryRelation', 'reviews']);

        // Search filter
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('categoryRelation', function($cQ) use ($search) {
                      $cQ->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Category / Type filter
        if ($request->filled('category') && $request->input('category') !== 'all') {
            $cat = $request->input('category');
            if ($cat === 'workspace') {
                $query->where('is_google_workspace', true);
            } elseif ($cat === '3rdparty') {
                $query->where('is_google_workspace', false);
            } elseif ($cat === 'favs') {
                $favs = $request->input('favs', []);
                if (is_array($favs) && count($favs) > 0) {
                    $query->where(function($q) use ($favs) {
                        $q->whereIn('id', $favs)
                          ->orWhereIn('name', $favs);
                    });
                } else {
                    $query->whereRaw('1 = 0');
                }
            } else {
                $query->whereHas('categoryRelation', function($q) use ($cat) {
                    $q->where('name', $cat);
                });
            }
        }

        // Hide AI Detection category/tools from students and public guests
        if (! $canSeeDetection) {
            $query->whereHas('categoryRelation', function($q) {
                $q->where('slug', '!=', 'ai-detection');
            });
        }

        $tools = $query->latest()->paginate(12)->withQueryString();

        $categories = Cache::rememberForever('welcome_categories', function () {
            return Category::withCount('approvedTools')
                ->orderBy('sort_order')
                ->get();
        });

        if ($categories instanceof \__PHP_Incomplete_Class || !is_iterable($categories)) {
            Cache::forget('welcome_categories');
            $categories = Category::withCount('approvedTools')
                ->orderBy('sort_order')
                ->get();
        }

        if (! $canSeeDetection) {
            $categories = $categories->filter(fn($c) => $c->slug !== 'ai-detection')->values();
        }

        // Official tools query (unpaginated, featured)
        $officialTools = Tool::approved()
            ->where('is_official', true)
            ->with(['categoryRelation', 'reviews'])
            ->when(! $canSeeDetection, function($q) {
                $q->whereHas('categoryRelation', function($cQ) {
                    $cQ->where('slug', '!=', 'ai-detection');
                });
            })
            ->latest()
            ->get();

        // Trending tools query (unpaginated, featured)
        $trendingTools = Tool::approved()
            ->with(['categoryRelation', 'reviews'])
            ->when(! $canSeeDetection, function($q) {
                $q->whereHas('categoryRelation', function($cQ) {
                    $cQ->where('slug', '!=', 'ai-detection');
                });
            })
            ->orderByDesc('click_count')
            ->take(4)
            ->get();

        $latestTool = Tool::approved()
            ->when(! $canSeeDetection, function($q) {
                $q->whereHas('categoryRelation', function($cQ) {
                    $cQ->where('slug', '!=', 'ai-detection');
                });
            })
            ->latest()
            ->first();

        // If AJAX request, return HTML segments
        if ($request->ajax() || $request->input('ajax') == 1) {
            return response()->json([
                'html' => view('partials.tool_grid', compact('tools'))->render(),
                'pagination' => $tools->links()->render(),
                'total' => $tools->total()
            ]);
        }

        return view('welcome', compact(
            'tools', 'categories', 'latestTool', 'trendingTools', 'officialTools', 
            'lessonPlanCount', 'badgeCount', 'latestBadges', 'favoriteTools', 'favoriteToolsCount',
            'recentLessonPlans', 'studentPrompts', 'teacherPrompts',
            'pendingRequests', 'pendingEvidence', 'pendingSuggestions',
            'pendingRequestsCount', 'pendingEvidenceCount', 'pendingSuggestionsCount'
        ));
    }
}
