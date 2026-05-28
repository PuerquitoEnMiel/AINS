<?php

namespace App\Services;

/**
 * PromptGuard — Lightweight guardrail that detects prompt injection
 * patterns in user-submitted chatbot messages before they reach the LLM.
 *
 * Returns a sanitized message or flags the input as malicious.
 */
class PromptGuard
{
    /**
     * Regex patterns that indicate prompt injection attempts.
     * Each pattern is case-insensitive and matches common evasion phrases.
     */
    protected static array $injectionPatterns = [
        // Direct instruction override attempts
        '/ignor(?:a|e)\s+(?:\w+\s+){0,3}(instrucciones|instructions|rules|reglas)/iu',
        '/olvid(?:a|e)\s+(?:\w+\s+){0,3}(instrucciones|instructions|rules|reglas)/iu',
        '/forget\s+(?:\w+\s+){0,3}(instructions|rules|constraints|guidelines|prompt|role)/iu',
        '/ignore\s+(?:\w+\s+){0,3}(instructions|rules|constraints|guidelines|prompt|role)/iu',
        '/disregard\s+(?:\w+\s+){0,3}(instructions|rules|constraints|guidelines|prompt|role)/iu',
        '/override\s+(?:\w+\s+){0,3}(instructions|rules|constraints|guidelines|prompt|role)/iu',

        // Role reassignment / jailbreaks
        '/you\s+are\s+now\s+(a|an|my|the)\s+/iu',
        '/act\s+as\s+(a|an|if|my|the)\s+/iu',
        '/pretend\s+(you\s+are|to\s+be|you\'?re)\s+/iu',
        '/ahora\s+eres\s+(un|una|mi)\s+/iu',
        '/actúa\s+como\s+(un|una|si)\s+/iu',
        '/finge\s+(ser|que\s+eres)\s+/iu',

        // System prompt extraction
        '/(?:show|reveal|display|print|output|repeat|echo)\s+(?:me\s+)?(?:your|the)\s+(?:system|initial|original|hidden|secret)\s+(?:prompt|instructions|message|role)/iu',
        '/(?:muestra|revela|imprime|repite)\s+(?:tu|el|la|las|los)\s+(?:instruccion|prompt|mensaje|sistema)/iu',
        '/what\s+(?:is|are)\s+your\s+(?:system|initial|original|hidden|secret)\s+(?:prompt|instructions|message)/iu',

        // DAN / jailbreak named patterns
        '/\bDAN\b/u',
        '/\bjailbreak/iu',
        '/\bDeveloper\s+Mode/iu',
        '/\bModo\s+Desarrollador/iu',

        // Delimiter / format injection (attempting to inject new system blocks)
        '/\[SYSTEM\]/iu',
        '/\[INST\]/iu',
        '/<<SYS>>/iu',
        '/<\|im_start\|>/iu',
        '/\bsystem\s*:\s*\n/iu',

        // Multi-step evasion ("first do X, then forget")
        '/(?:first|primero)\s+.*(?:then|luego|después)\s+.*(?:forget|ignore|olvida|ignora)/iu',
    ];

    /**
     * Dangerous content patterns — explicit harmful content requests.
     */
    protected static array $harmfulPatterns = [
        '/(?:how\s+to|como)\s+(?:hack|hackear|crack|exploit|explotar)/iu',
        '/(?:write|genera|crea|make)\s+(?:me\s+)?(?:a\s+)?(?:malware|virus|trojan|ransomware|exploit|keylogger)/iu',
        '/(?:give|dame|dime)\s+(?:me\s+)?(?:personal|private|confidential)\s+(?:data|information|datos|información)/iu',
    ];

    /**
     * Check if a message contains prompt injection patterns.
     *
     * @param  string  $message  Raw user message
     * @return array{safe: bool, reason: string|null, category: string|null}
     */
    public static function analyze(string $message): array
    {
        $normalized = self::normalize($message);

        // Check injection patterns
        foreach (self::$injectionPatterns as $pattern) {
            if (preg_match($pattern, $normalized)) {
                return [
                    'safe' => false,
                    'reason' => 'prompt_injection',
                    'category' => 'injection',
                ];
            }
        }

        // Check harmful content
        foreach (self::$harmfulPatterns as $pattern) {
            if (preg_match($pattern, $normalized)) {
                return [
                    'safe' => false,
                    'reason' => 'harmful_content',
                    'category' => 'harmful',
                ];
            }
        }

        return [
            'safe' => true,
            'reason' => null,
            'category' => null,
        ];
    }

    /**
     * Normalize message for analysis: collapse whitespace, strip invisible
     * Unicode characters, and normalize homoglyphs that could evade detection.
     */
    protected static function normalize(string $message): string
    {
        // Strip zero-width and invisible Unicode characters
        $message = preg_replace('/[\x{200B}-\x{200F}\x{2028}-\x{202F}\x{2060}-\x{206F}\x{FEFF}]/u', '', $message);

        // Collapse multiple spaces/newlines into single space
        $message = preg_replace('/\s+/', ' ', $message);

        // Common homoglyph replacements (Cyrillic/Greek lookalikes → Latin)
        $homoglyphs = [
            'а' => 'a', 'е' => 'e', 'о' => 'o', 'р' => 'p',
            'с' => 'c', 'у' => 'y', 'х' => 'x', 'і' => 'i',
            'ε' => 'e', 'ο' => 'o', 'ρ' => 'p', 'α' => 'a',
        ];
        $message = strtr($message, $homoglyphs);

        return trim($message);
    }

    /**
     * Get a friendly rejection message based on the category.
     */
    public static function getRejectionMessage(string $category, string $firstName): string
    {
        return match ($category) {
            'injection' => "🛡️ **{$firstName}**, that request appears to contain instructions that could alter my behavior. As your AINS AI Companion, I'm designed to stay focused on helping you with educational technology and approved tools. Please rephrase your question and I'll be happy to assist! 😊",
            'harmful'   => "⚠️ **{$firstName}**, I can't help with that type of request. My purpose is to support your educational journey at ANS. Let me know if you have questions about approved tools, study strategies, or tech integration! 📚",
            default     => "🤖 **{$firstName}**, I couldn't process that request. Please try rephrasing your question about educational technology or approved tools.",
        };
    }

    /**
     * Gemini API safety settings payload.
     *
     * Injected into every generateContent request so the model-level
     * safety filters act as a second layer on top of PromptGuard regex.
     *
     * @return array<int, array{category: string, threshold: string}>
     */
    public static function geminiSafetySettings(): array
    {
        return [
            [
                'category'  => 'HARM_CATEGORY_HARASSMENT',
                'threshold' => 'BLOCK_MEDIUM_AND_ABOVE',
            ],
            [
                'category'  => 'HARM_CATEGORY_HATE_SPEECH',
                'threshold' => 'BLOCK_MEDIUM_AND_ABOVE',
            ],
            [
                'category'  => 'HARM_CATEGORY_SEXUALLY_EXPLICIT',
                'threshold' => 'BLOCK_MEDIUM_AND_ABOVE',
            ],
            [
                'category'  => 'HARM_CATEGORY_DANGEROUS_CONTENT',
                'threshold' => 'BLOCK_MEDIUM_AND_ABOVE',
            ],
        ];
    }
}
