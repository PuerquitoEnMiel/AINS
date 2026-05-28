<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * GoogleDocsService — Handles all Google Docs API interactions.
 *
 * Extracted from LessonPlanController to keep the controller thin
 * and make this logic independently testable.
 */
class GoogleDocsService
{
    protected string $baseUrl = 'https://docs.googleapis.com/v1/documents';

    /**
     * Create a new Google Doc and populate it with the lesson plan content.
     *
     * @param  string  $token    OAuth2 access token
     * @param  string  $title    Document title
     * @param  string  $markdown Markdown content to convert and insert
     * @return array{success: bool, url?: string, error?: string, reauth?: bool}
     */
    public function createFromMarkdown(string $token, string $title, string $markdown): array
    {
        // Step 1: Create empty document
        $createResponse = Http::timeout(20)
            ->withHeaders([
                'Authorization' => "Bearer {$token}",
                'Content-Type'  => 'application/json',
            ])
            ->when(config('services.google.disable_ssl_verify', false), fn ($http) => $http->withoutVerifying())
            ->post($this->baseUrl, ['title' => $title]);

        if ($createResponse->status() === 401) {
            return ['success' => false, 'reauth' => true];
        }

        if (! $createResponse->successful()) {
            Log::error('GoogleDocsService: Create Error — ' . $createResponse->body());
            return ['success' => false, 'error' => 'Error creating the document in Google Docs.'];
        }

        $documentId = $createResponse->json('documentId');

        // Step 2: Populate with content
        $requests = $this->buildRequests($markdown);

        if (! empty($requests)) {
            $updateResponse = Http::timeout(20)
                ->withHeaders([
                    'Authorization' => "Bearer {$token}",
                    'Content-Type'  => 'application/json',
                ])
                ->when(config('services.google.disable_ssl_verify', false), fn ($http) => $http->withoutVerifying())
                ->post("{$this->baseUrl}/{$documentId}:batchUpdate", ['requests' => $requests]);

            if (! $updateResponse->successful()) {
                Log::warning('GoogleDocsService: batchUpdate warning — ' . $updateResponse->body());
            }
        }

        return [
            'success' => true,
            'url'     => "https://docs.google.com/document/d/{$documentId}/edit",
        ];
    }

    // ── Private: Markdown → Google Docs API Requests ────────────

    /**
     * Convert markdown text to an array of Google Docs API batchUpdate requests.
     * Handles headings (h1–h6), bullet lists, and strips **bold** markers.
     *
     * @return array<int, array>
     */
    protected function buildRequests(string $markdown): array
    {
        $lines        = explode("\n", $markdown);
        $fullText     = '';
        $styles       = [];
        $currentIndex = 1;

        foreach ($lines as $line) {
            $cleanLine = $line;
            $lineStyle = 'normal';

            if (preg_match('/^(#{1,6})\s+(.*)$/', trim($line), $matches)) {
                $level     = strlen($matches[1]);
                $cleanLine = $matches[2];
                $lineStyle = 'h' . $level;
            } elseif (str_starts_with(trim($line), '- ') || str_starts_with(trim($line), '* ')) {
                $cleanLine = '• ' . substr(trim($line), 2);
                $lineStyle = 'list';
            }

            // Strip bold/italic markdown
            $cleanLine   = str_replace(['**', '*'], '', $cleanLine);
            $textToInsert = $cleanLine . "\n";

            try {
                $converted = iconv('UTF-8', 'UTF-16LE', $textToInsert);
                $length    = $converted !== false ? strlen($converted) / 2 : mb_strlen($textToInsert);
            } catch (\Exception) {
                $length = mb_strlen($textToInsert);
            }

            if ($length > 0) {
                $styles[] = [
                    'start' => $currentIndex,
                    'end'   => $currentIndex + $length - 1,
                    'style' => $lineStyle,
                ];
                $fullText     .= $textToInsert;
                $currentIndex += $length;
            }
        }

        if (empty($fullText)) {
            return [];
        }

        $requests = [
            [
                'insertText' => [
                    'location' => ['index' => 1],
                    'text'     => $fullText,
                ],
            ],
        ];

        foreach ($styles as $s) {
            $styleRequest = $this->buildStyleRequest($s);
            if ($styleRequest !== null) {
                $requests[] = $styleRequest;
            }
        }

        return $requests;
    }

    /**
     * Build a single updateTextStyle request for a heading level.
     */
    protected function buildStyleRequest(array $s): ?array
    {
        $styleMap = [
            'h1' => ['size' => 20, 'color' => ['red' => 0.08, 'green' => 0.38, 'blue' => 0.31]],
            'h2' => ['size' => 15, 'color' => ['red' => 0.90, 'green' => 0.46, 'blue' => 0.00]],
            'h3' => ['size' => 12, 'color' => ['red' => 0.08, 'green' => 0.38, 'blue' => 0.31]],
            'h4' => ['size' => 11, 'color' => ['red' => 0.20, 'green' => 0.20, 'blue' => 0.20]],
            'h5' => ['size' => 11, 'color' => ['red' => 0.20, 'green' => 0.20, 'blue' => 0.20]],
            'h6' => ['size' => 11, 'color' => ['red' => 0.20, 'green' => 0.20, 'blue' => 0.20]],
        ];

        if (! isset($styleMap[$s['style']])) {
            return null;
        }

        $cfg = $styleMap[$s['style']];

        return [
            'updateTextStyle' => [
                'range'     => ['startIndex' => $s['start'], 'endIndex' => $s['end']],
                'textStyle' => [
                    'fontSize'        => ['magnitude' => $cfg['size'], 'unit' => 'PT'],
                    'bold'            => true,
                    'foregroundColor' => ['color' => ['rgbColor' => $cfg['color']]],
                ],
                'fields' => 'fontSize,bold,foregroundColor',
            ],
        ];
    }
}
