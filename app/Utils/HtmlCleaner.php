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
     * Minify HTML by applying all possible size reduction techniques.
     * Extends clean() with additional minification steps.
     *
     * @param  string  $html  The raw HTML content
     * @param  int|null  $maxLength  Maximum length for the HTML (default: 50000)
     * @return string  Minified HTML with reduced size
     */
    public static function minify(string $html, ?int $maxLength = null): string
    {
        // Start with clean() processing
        $html = self::clean($html, null); // Don't truncate yet, we'll do it at the end

        // Remove HTML comments
        $html = self::removeComments($html);

        // Remove empty tags
        $html = self::removeEmptyTags($html);

        // Aggressively compress whitespace (more than normalize)
        $html = self::compressWhitespace($html);

        // Remove whitespace around tags
        $html = self::removeWhitespaceAroundTags($html);

        // Truncate if necessary
        $maxLength = $maxLength ?? 50000;
        $html = self::truncate($html, $maxLength);

        return $html;
    }

    /**
     * Sanitize HTML by removing all attributes, keeping only content.
     * Extends minify() with attribute removal.
     *
     * @param  string  $html  The raw HTML content
     * @param  int|null  $maxLength  Maximum length for the HTML (default: 50000)
     * @return string  Sanitized HTML with only content, no attributes
     */
    public static function sanitize(string $html, ?int $maxLength = null): string
    {
        // Start with minify() processing
        $html = self::minify($html, null); // Don't truncate yet, we'll do it at the end

        // Remove all attributes from HTML tags
        $html = self::removeAllAttributes($html);

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

    /**
     * Remove HTML comments.
     */
    protected static function removeComments(string $html): string
    {
        return preg_replace('/<!--.*?-->/s', '', $html);
    }

    /**
     * Remove empty HTML tags (tags with no content).
     */
    protected static function removeEmptyTags(string $html): string
    {
        // Common empty tags that can be safely removed
        $emptyTags = [
            'p', 'div', 'span', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6',
            'li', 'td', 'th', 'dt', 'dd', 'section', 'article', 'aside',
            'header', 'footer', 'nav', 'main', 'figure', 'figcaption',
        ];

        foreach ($emptyTags as $tag) {
            // Match opening and closing tags with optional whitespace between them
            $pattern = '/<'.$tag.'(?:\s[^>]*)?>\s*<\/'.$tag.'>/i';
            $html = preg_replace($pattern, '', $html);
        }

        return $html;
    }

    /**
     * Compress whitespace more aggressively than normalize.
     */
    protected static function compressWhitespace(string $html): string
    {
        // Replace all whitespace (including newlines, tabs) with a single space
        $html = preg_replace('/\s+/', ' ', $html);

        // Trim leading and trailing whitespace
        return trim($html);
    }

    /**
     * Remove whitespace around HTML tags.
     */
    protected static function removeWhitespaceAroundTags(string $html): string
    {
        // Remove spaces before closing tags
        $html = preg_replace('/\s+<\//', '</', $html);

        // Remove spaces after opening tags
        $html = preg_replace('/>\s+/', '>', $html);

        // Remove spaces between tags
        $html = preg_replace('/>\s+</', '><', $html);

        return $html;
    }

    /**
     * Remove all attributes from HTML tags, keeping only tag names and content.
     */
    protected static function removeAllAttributes(string $html): string
    {
        // Handle self-closing tags first (like <img />, <br />, etc.)
        // Pattern: <tag attribute="value" /> -> <tag />
        $html = preg_replace('/<(\w+)(\s+[^>]*)?\s*\/>/i', '<$1 />', $html);

        // Match opening tags and remove all attributes
        // Pattern: <tag attribute="value" attribute2='value2' attribute3> -> <tag>
        $html = preg_replace('/<(\w+)(\s+[^>]*)?>/i', '<$1>', $html);

        return $html;
    }
}
