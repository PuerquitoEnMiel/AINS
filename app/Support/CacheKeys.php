<?php

namespace App\Support;

/**
 * CacheKeys — Single source of truth for all cache key strings.
 *
 * Use these constants instead of hard-coded strings to prevent
 * typos and make cache invalidation easier to trace.
 */
final class CacheKeys
{
    /** Tool catalog displayed on the welcome/home page. */
    public const WELCOME_TOOLS = 'welcome_tools';

    /** Category list with tool counts displayed on the welcome page. */
    public const WELCOME_CATEGORIES = 'welcome_categories';

    /** Chatbot system instruction loaded from storage. */
    public const CHATBOT_INSTRUCTION = 'chatbot_instruction';

    /** Prefix for per-user badge collections (append user ID). */
    public const USER_BADGES_PREFIX = 'user_badges_';

    /**
     * Generate a per-user badges cache key.
     */
    public static function userBadges(int $userId): string
    {
        return self::USER_BADGES_PREFIX . $userId;
    }
}
