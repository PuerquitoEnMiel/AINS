<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * GeminiService — Centralized client for the Google Gemini API.
 *
 * Handles model fallback, retry logic, SSL verification per environment,
 * and injects Gemini safety settings on every request.
 *
 * Usage:
 *   $text = app(GeminiService::class)->generate($prompt);
 *   $reply = app(GeminiService::class)->chat($contents, $systemInstruction);
 */
class GeminiService
{
    protected string $apiKey;

    /** Ordered list of models to try. First success wins. */
    protected array $models;

    protected int $timeout;
    protected int $chatTimeout;

    public function __construct()
    {
        $this->apiKey      = config('services.gemini.api_key', '');
        $this->models      = config('services.gemini.models', ['gemini-2.5-flash']);
        $this->timeout     = config('services.gemini.timeout', 60);
        $this->chatTimeout = config('services.gemini.chat_timeout', 25);
    }

    // ── Public API ──────────────────────────────────────────────

    /**
     * Check if the API key is configured.
     */
    public function isConfigured(): bool
    {
        return !empty($this->apiKey);
    }

    /**
     * Simple text generation (lesson plans, refinements, insights).
     *
     * @param  array<int, array>  $parts  Gemini parts array (text + optional inlineData)
     * @param  int|null  $timeoutOverride  Custom timeout in seconds
     * @return string|null  Generated text, or null on failure
     */
    public function generate(array $parts, ?int $timeoutOverride = null): ?string
    {
        $payload = [
            'contents' => [
                [
                    'role'  => 'user',
                    'parts' => $parts,
                ],
            ],
            'safetySettings' => PromptGuard::geminiSafetySettings(),
        ];

        return $this->callWithFallback($payload, $timeoutOverride ?? $this->timeout);
    }

    /**
     * Multi-turn chat (chatbot conversations).
     *
     * @param  array<int, array>  $contents         Alternating user/model turns
     * @param  string             $systemInstruction System prompt
     * @return string|null
     */
    public function chat(array $contents, string $systemInstruction): ?string
    {
        $payload = [
            'contents'          => $contents,
            'systemInstruction' => [
                'parts' => [['text' => $systemInstruction]],
            ],
            'safetySettings' => PromptGuard::geminiSafetySettings(),
        ];

        return $this->callWithFallback($payload, $this->chatTimeout);
    }

    // ── Internal ────────────────────────────────────────────────

    /**
     * Try each model in order until one succeeds. Returns generated text or null.
     */
    protected function callWithFallback(array $payload, int $timeout): ?string
    {
        $lastError = '';

        foreach ($this->models as $model) {
            try {
                $response = Http::timeout($timeout)
                    ->when(app()->isLocal(), fn ($http) => $http->withoutVerifying())
                    ->retry(2, 1000)
                    ->post(
                        "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$this->apiKey}",
                        $payload
                    );

                if ($response->successful()) {
                    $text = $response->json('candidates.0.content.parts.0.text');
                    if ($text) {
                        Log::debug("GeminiService: success with model {$model}");
                        return $text;
                    }
                }

                $lastError = "Status {$response->status()} — " . $response->body();
                Log::warning("GeminiService: model {$model} failed — {$lastError}");

            } catch (\Exception $e) {
                $lastError = $e->getMessage();
                Log::warning("GeminiService: model {$model} exception — {$lastError}");
            }
        }

        Log::error("GeminiService: all models failed. Last error: {$lastError}");

        return null;
    }
}
