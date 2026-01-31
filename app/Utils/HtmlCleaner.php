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
        $html = self::truncate($html, $maxLength);

        return $html;
    }

    /**
     * Minify HTML by applying all possible size reduction techniques.
     *
     * @param  string  $html  The raw HTML content
     * @param  int|null  $maxLength  Maximum length for the HTML (default: 50000)
     * @return string  Minified HTML with reduced size
     */
    public static function minify(string $html, ?int $maxLength = null): string
    {
        // Remove HTML comments
        $html = self::removeComments($html);

        // Aggressively compress whitespace (more than normalize)
        $html = self::compressWhitespace($html);

        // Remove whitespace around tags
        $html = self::removeWhitespaceAroundTags($html);

        // Decode HTML entities
        $html = self::decodeEntities($html);

        // Truncate if necessary
        return self::truncate($html, $maxLength);
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
        // Remove all attributes from HTML tags
        $html = self::removeAllAttributes($html);

        // Minifying it
        $html = self::minify($html, $maxLength);

        // Clean it
        $html = self::clean($html, $maxLength);

        // Remove empty tags
        $html = self::removeEmptyTags($html);

        // Truncate if necessary
        return self::truncate($html, $maxLength);
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
    protected static function truncate(string $html, ?int $maxLength = null): string
    {
        if ($maxLength !== null and strlen($html) > $maxLength) {
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
        // 1) Remove self-closing tags ONLY when they have NO attributes:
        // <br />  or <img/>  (no attrs) remove
        // <img src="x" /> keep
        $html = preg_replace(
            '/<([a-z][a-z0-9:-]*)\s*\/\s*>/i',
            '',
            $html
        ) ?? $html;

        // Precompute which tags have a closing tag somewhere: </tag>
        preg_match_all('/<\/\s*([a-z][a-z0-9:-]*)\s*>/i', $html, $m);
        $hasClosing = [];
        foreach ($m[1] as $t) {
            $hasClosing[strtolower($t)] = true;
        }

        // 2) Remove opening tags that have NO closing tag anywhere in the doc
        // ONLY when they have NO attributes:
        // <meta> remove
        // <meta content="..."> keep
        $html = preg_replace_callback(
            '/<\s*([a-z][a-z0-9:-]*)\b([^>]*)>/i',
            static function (array $match) use ($hasClosing): string {
                $tag = strtolower($match[1]);
                $rawAttrs = $match[2] ?? '';

                // If this tag has a closing somewhere, keep it (it might be a container)
                if (isset($hasClosing[$tag])) {
                    return $match[0];
                }

                // If it has any non-whitespace in attribute chunk -> keep
                // Example rawAttrs: ' content="x"' or ' href=#'
                if (trim($rawAttrs) !== '') {
                    return $match[0];
                }

                // Otherwise it's a naked void/unclosed tag like <meta> -> remove
                return '';
            },
            $html
        ) ?? $html;

        // 3) Remove empty container pairs recursively
        // ONLY when opening tag has NO attributes:
        // <div></div> remove
        // <div class="x"></div> keep
        $prev = null;
        while ($prev !== $html) {
            $prev = $html;

            $html = preg_replace(
                '/<\s*([a-z][a-z0-9:-]*)\s*>(?:\s|&nbsp;|&#160;|&#xA0;)*<\/\s*\1\s*>/i',
                '',
                $html
            ) ?? $html;
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
     * Remove all attributes from HTML tags, but keep a safe whitelist of important attributes.
     *
     * @param string $html
     * @param array<string> $keepAttributes Attributes to preserve (case-insensitive), e.g. ['href','src'].
     */
    protected static function removeAllAttributes(string $html, array $keepAttributes = [
        // Links & navigation
        'href', 'rel', 'target',

        // Media / resources
        'src', 'srcset', 'sizes', 'alt',

        // Meta / SEO / document hints
        'content', 'name', 'property', 'charset', 'http-equiv',

        // Usability / accessibility
        'title', 'aria-label', 'role',

        // Semantics sometimes useful when extracting structured content
        'datetime',

        // Images/iframes sizing can matter for downstream rendering heuristics
        'width', 'height',
    ]): string {
        // Normalize whitelist to lowercase lookup table for fast checks
        $keep = [];
        foreach ($keepAttributes as $a) {
            $keep[strtolower($a)] = true;
        }

        $pattern = '/<(?!!--|!DOCTYPE|\?)(?!\/)([a-z][a-z0-9:-]*)\b([^<>]*?)(\/?)>/i';

        $result = preg_replace_callback(
            $pattern,
            static function (array $m) use ($keep): string {
                $tag = $m[1];
                $attrChunk = $m[2] ?? '';
                $selfClosing = ($m[3] ?? '') === '/';

                $keptPairs = [];

                // Match attributes:
                // - key="value"
                // - key='value'
                // - key=value
                // - boolean key
                if ($attrChunk !== '') {
                    preg_match_all(
                        '/([a-z_:][a-z0-9:._-]*)(?:\s*=\s*(?:"([^"]*)"|\'([^\']*)\'|([^\s"\'>\/=]+)))?/i',
                        $attrChunk,
                        $am,
                        PREG_SET_ORDER
                    );

                    foreach ($am as $a) {
                        $name = $a[1];
                        $lname = strtolower($name);

                        if (!isset($keep[$lname])) {
                            continue;
                        }

                        // Value can be in group 2/3/4; if none => boolean attribute
                        $value = $a[2] ?? ($a[3] ?? ($a[4] ?? null));

                        if ($value === null) {
                            // boolean attribute like "disabled"
                            $keptPairs[] = $name;
                        } else {
                            // Escape quotes minimally; we always output double quotes
                            $escaped = htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
                            $keptPairs[] = $name . '="' . $escaped . '"';
                        }
                    }
                }

                $attrsOut = $keptPairs ? (' ' . implode(' ', $keptPairs)) : '';
                return $selfClosing ? "<{$tag}{$attrsOut} />" : "<{$tag}{$attrsOut}>";
            },
            $html
        );

        return $result ?? $html;
    }
}
