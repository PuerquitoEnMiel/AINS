<?php

namespace Tests\Unit;

use App\Services\GeminiService;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class GeminiServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['services.gemini.api_key' => 'test-api-key']);
        config(['services.gemini.models' => ['gemini-2.5-flash', 'gemini-1.5-flash']]);
        config(['services.gemini.timeout' => 60]);
        config(['services.gemini.chat_timeout' => 25]);
    }

    public function test_is_configured_returns_true_when_key_set(): void
    {
        $service = new GeminiService();
        $this->assertTrue($service->isConfigured());
    }

    public function test_is_configured_returns_false_when_key_empty(): void
    {
        config(['services.gemini.api_key' => '']);
        $service = new GeminiService();
        $this->assertFalse($service->isConfigured());
    }

    public function test_generate_returns_text_on_success(): void
    {
        Http::fake([
            '*gemini-2.5-flash*' => Http::response([
                'candidates' => [[
                    'content' => ['parts' => [['text' => 'Generated lesson plan content']]],
                ]],
            ], 200),
        ]);

        $service = new GeminiService();
        $result  = $service->generate([['text' => 'Write a lesson plan']]);

        $this->assertSame('Generated lesson plan content', $result);
    }

    public function test_generate_falls_back_to_second_model_on_first_failure(): void
    {
        Http::fake([
            '*gemini-2.5-flash*' => Http::response(['error' => 'overloaded'], 503),
            '*gemini-1.5-flash*' => Http::response([
                'candidates' => [[
                    'content' => ['parts' => [['text' => 'Fallback response']]],
                ]],
            ], 200),
        ]);

        $service = new GeminiService();
        $result  = $service->generate([['text' => 'test prompt']]);

        $this->assertSame('Fallback response', $result);
    }

    public function test_generate_returns_null_when_all_models_fail(): void
    {
        Http::fake([
            '*' => Http::response(['error' => 'service unavailable'], 503),
        ]);

        Log::shouldReceive('error')->once();
        Log::shouldReceive('warning')->twice(); // one per model

        $service = new GeminiService();
        $result  = $service->generate([['text' => 'test']]);

        $this->assertNull($result);
    }

    public function test_chat_sends_contents_and_system_instruction(): void
    {
        Http::fake([
            '*' => Http::response([
                'candidates' => [[
                    'content' => ['parts' => [['text' => 'Chat reply']]],
                ]],
            ], 200),
        ]);

        $service = new GeminiService();
        $result  = $service->chat(
            [['role' => 'user', 'parts' => [['text' => 'Hello']]]],
            'You are an EdTech assistant.'
        );

        $this->assertSame('Chat reply', $result);

        Http::assertSent(function (Request $request) {
            $body = $request->data();
            return isset($body['systemInstruction']) && isset($body['safetySettings']);
        });
    }

    public function test_safety_settings_included_in_every_request(): void
    {
        Http::fake([
            '*' => Http::response([
                'candidates' => [[
                    'content' => ['parts' => [['text' => 'ok']]],
                ]],
            ], 200),
        ]);

        $service = new GeminiService();
        $service->generate([['text' => 'test']]);

        Http::assertSent(function (Request $request) {
            $body = $request->data();
            return isset($body['safetySettings']) && count($body['safetySettings']) === 4;
        });
    }
}
