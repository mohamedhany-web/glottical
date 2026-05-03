<?php

namespace App\Support;

/**
 * Sanitize user-provided search strings for SQL LIKE clauses.
 * Uses PCRE Unicode properties (not invalid \\u0600 escapes in PHP regex).
 */
final class SearchInput
{
    public static function sanitizeForLike(string $value, int $maxLength = 255): string
    {
        $value = strip_tags(trim($value));
        if ($value === '') {
            return '';
        }

        $cleaned = preg_replace('/[^\p{L}\p{N}\s@._-]/u', '', $value);
        if ($cleaned === null) {
            $cleaned = preg_replace('/[^a-zA-Z0-9\s@._-]/', '', $value) ?? '';
        }

        if (function_exists('mb_strlen') && mb_strlen($cleaned) > $maxLength) {
            return mb_substr($cleaned, 0, $maxLength);
        }

        return strlen($cleaned) > $maxLength ? substr($cleaned, 0, $maxLength) : $cleaned;
    }
}
