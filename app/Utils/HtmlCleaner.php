<?php

namespace App\Utils;

class HtmlCleaner
{
    /**
     * Clean HTML for classification by removing scripts/styles, decoding entities, and normalizing.
     *
     * @param  string  $html  The raw HTML content
     * @param  int|null  $maxLength  Maximum length for the HTML (default: 50000)
     * @return string  Cleaned HTML ready for classification
     */
    public static function clean(string $html, ?int $maxLength = null): string
    {
        // Remove script and style tags
        $html = self::removeScriptsAndStyles($html);

        // Decode HTML entities
        $html = self::decodeEntities($html);

        // Normalize whitespace
        $html = self::normalizeWhitespace($html);

        // Truncate if necessary
        $maxLength = $maxLength ?? 50000;
        $html = self::truncate($html, $maxLength);

        return $html;
    }

    /**
     * Remove script and style tags from HTML.
     */
    protected static function removeScriptsAndStyles(string $html): string
    {
        // Remove script tags
        $html = preg_replace('/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/mi', '', $html);

        // Remove style tags
        $html = preg_replace('/<style\b[^<]*(?:(?!<\/style>)<[^<]*)*<\/style>/mi', '', $html);

        return $html;
    }

    /**
     * Decode HTML entities.
     */
    protected static function decodeEntities(string $html): string
    {
        return html_entity_decode($html, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }

    /**
     * Normalize whitespace in HTML.
     */
    protected static function normalizeWhitespace(string $html): string
    {
        // Replace multiple whitespace characters with a single space
        $html = preg_replace('/\s+/', ' ', $html);

        // Trim leading and trailing whitespace
        return trim($html);
    }

    /**
     * Truncate HTML to a maximum length if necessary.
     */
    protected static function truncate(string $html, int $maxLength): string
    {
        if (strlen($html) > $maxLength) {
            return substr($html, 0, $maxLength).'... [truncated]';
        }

        return $html;
    }
}
