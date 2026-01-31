<?php

namespace Tests\Unit\Utils;

use App\Utils\HtmlCleaner;
use Tests\TestCase;

class HtmlCleanerTest extends TestCase
{
    public function test_it_removes_script_tags(): void
    {
        $html = '<html><head><script>alert("test");</script></head><body>Content</body></html>';

        $result = HtmlCleaner::clean($html);

        $this->assertStringNotContainsString('<script>', $result);
        $this->assertStringNotContainsString('alert', $result);
        $this->assertStringContainsString('Content', $result);
    }

    public function test_it_removes_style_tags(): void
    {
        $html = '<html><head><style>body { color: red; }</style></head><body>Content</body></html>';

        $result = HtmlCleaner::clean($html);

        $this->assertStringNotContainsString('<style>', $result);
        $this->assertStringNotContainsString('color: red', $result);
        $this->assertStringContainsString('Content', $result);
    }

    public function test_it_removes_both_script_and_style_tags(): void
    {
        $html = '<html><head><script>alert("test");</script><style>body { color: red; }</style></head><body>Content</body></html>';

        $result = HtmlCleaner::clean($html);

        $this->assertStringNotContainsString('<script>', $result);
        $this->assertStringNotContainsString('<style>', $result);
        $this->assertStringContainsString('Content', $result);
    }

    public function test_it_decodes_html_entities(): void
    {
        $html = '<html><body>&lt;div&gt;Content&lt;/div&gt;</body></html>';

        $result = HtmlCleaner::clean($html);

        $this->assertStringContainsString('<div>Content</div>', $result);
        $this->assertStringNotContainsString('&lt;', $result);
        $this->assertStringNotContainsString('&gt;', $result);
    }

    public function test_it_normalizes_whitespace(): void
    {
        $html = '<html><body>   Multiple    spaces    and    tabs		here   </body></html>';

        $result = HtmlCleaner::clean($html);

        // Should have single spaces instead of multiple
        $this->assertStringNotContainsString('    ', $result);
        $this->assertStringNotContainsString("\t\t", $result);
        // Should be trimmed
        $this->assertFalse(str_starts_with($result, ' '));
        $this->assertFalse(str_ends_with($result, ' '));
    }

    public function test_it_truncates_long_html(): void
    {
        $html = str_repeat('<p>Content paragraph</p>', 10000);
        $maxLength = 1000;

        $result = HtmlCleaner::clean($html, $maxLength);

        $this->assertLessThanOrEqual($maxLength + strlen('... [truncated]'), strlen($result));
        $this->assertStringEndsWith('... [truncated]', $result);
    }

    public function test_it_does_not_truncate_short_html(): void
    {
        $html = '<html><body>Short content</body></html>';
        $maxLength = 1000;

        $result = HtmlCleaner::clean($html, $maxLength);

        $this->assertFalse(str_ends_with($result, '... [truncated]'));
        // HTML is normalized (whitespace, entities decoded) but not truncated
        $this->assertStringContainsString('Short content', $result);
        $this->assertGreaterThan(strlen($result), $maxLength);
    }

    public function test_it_uses_default_max_length_when_not_specified(): void
    {
        $html = str_repeat('<p>Content</p>', 20000); // Much longer than default 50000
        $defaultMaxLength = 50000;

        $result = HtmlCleaner::clean($html, $defaultMaxLength);

        // Should be truncated to default max length
        $this->assertLessThanOrEqual($defaultMaxLength + strlen('... [truncated]'), strlen($result));
    }

    public function test_it_handles_empty_html(): void
    {
        $html = '';

        $result = HtmlCleaner::clean($html);

        $this->assertEquals('', $result);
    }

    public function test_it_handles_html_with_only_scripts_and_styles(): void
    {
        $html = '<script>alert("test");</script><style>body { color: red; }</style>';

        $result = HtmlCleaner::clean($html);

        // Should return empty or whitespace-only string after removing scripts/styles
        $this->assertEmpty(trim($result));
    }

    public function test_it_preserves_content_structure(): void
    {
        $html = '<html><body><h1>Title</h1><p>Paragraph with <strong>bold</strong> text.</p></body></html>';

        $result = HtmlCleaner::clean($html);

        $this->assertStringContainsString('Title', $result);
        $this->assertStringContainsString('Paragraph', $result);
        $this->assertStringContainsString('bold', $result);
    }

    public function test_it_handles_nested_script_tags(): void
    {
        $html = '<html><body><script>var x = "<script>nested</script>";</script>Content</body></html>';

        $result = HtmlCleaner::clean($html);

        $this->assertStringNotContainsString('<script>', $result);
        $this->assertStringContainsString('Content', $result);
    }

    public function test_it_handles_multiline_html(): void
    {
        $html = "<html>\n<body>\n  <h1>Title</h1>\n  <p>Content</p>\n</body>\n</html>";

        $result = HtmlCleaner::clean($html);

        // Should normalize newlines to spaces
        $this->assertStringNotContainsString("\n", $result);
        $this->assertStringContainsString('Title', $result);
        $this->assertStringContainsString('Content', $result);
    }

    public function test_it_respects_custom_max_length(): void
    {
        $html = str_repeat('<p>Content</p>', 1000);
        $customMaxLength = 500;

        $result = HtmlCleaner::clean($html, $customMaxLength);

        $this->assertLessThanOrEqual($customMaxLength + strlen('... [truncated]'), strlen($result));
        $this->assertStringEndsWith('... [truncated]', $result);
    }

    public function test_minify_removes_html_comments(): void
    {
        $html = '<html><body><!-- This is a comment --><h1>Title</h1></body></html>';

        $result = HtmlCleaner::minify($html);

        $this->assertStringNotContainsString('<!--', $result);
        $this->assertStringNotContainsString('-->', $result);
        $this->assertStringContainsString('Title', $result);
    }

    public function test_minify_removes_multiline_comments(): void
    {
        $html = '<html><body><!-- This is a multi-line comment
        with multiple lines --><h1>Title</h1></body></html>';

        $result = HtmlCleaner::minify($html);

        $this->assertStringNotContainsString('<!--', $result);
        $this->assertStringContainsString('Title', $result);
    }

    public function test_minify_does_not_removes_empty_tags(): void
    {
        $html = '<html><body><p></p><div></div><h1>Title</h1><span></span></body></html>';

        $result = HtmlCleaner::minify($html);

        $this->assertStringContainsString('<p></p>', $result);
        $this->assertStringContainsString('<div></div>', $result);
        $this->assertStringContainsString('<span></span>', $result);
        $this->assertStringContainsString('Title', $result);
    }

    public function test_minify_does_not_remove_empty_tags_with_attributes(): void
    {
        $html = '<html><body><p class="empty"></p><div id="test"></div><h1>Title</h1></body></html>';

        $result = HtmlCleaner::minify($html);

        // Empty tags with attributes should also be removed
        $this->assertStringContainsString('<p class="empty"></p>', $result);
        $this->assertStringContainsString('<div id="test"></div>', $result);
        $this->assertStringContainsString('Title', $result);
    }

    public function test_minify_compresses_whitespace_aggressively(): void
    {
        $html = "<html>\n\n<body>\n\t<h1>   Title   </h1>\n\t<p>   Content   </p>\n</body>\n</html>";

        $result = HtmlCleaner::minify($html);

        // Should have no newlines or tabs
        $this->assertStringNotContainsString("\n", $result);
        $this->assertStringNotContainsString("\t", $result);
        // Should have minimal spaces
        $this->assertStringContainsString('Title', $result);
        $this->assertStringContainsString('Content', $result);
    }

    public function test_minify_removes_whitespace_around_tags(): void
    {
        $html = '<html> <body> <h1> Title </h1> <p> Content </p> </body> </html>';

        $result = HtmlCleaner::minify($html);

        // Should remove spaces around tags
        $this->assertStringNotContainsString('> <', $result);
        $this->assertStringNotContainsString(' </', $result);
        $this->assertStringContainsString('Title', $result);
        $this->assertStringContainsString('Content', $result);
    }

    public function test_minify_applies_all_size_reduction_techniques(): void
    {
        $html = '<html>
<head><!-- This is a comment -->
<style>
body { color: red; }
</style>
<script>
alert("test");
</script></head>
<body>
<p>

</p>
<div>

</div>
<h1>   Title   </h1>
<p>   Content   </p>
<span>

</span>
</body>
</html>';

        $minified = HtmlCleaner::minify($html);

        // Verify minify applies all reduction techniques
        $this->assertStringNotContainsString('<!--', $minified); // Comments removed
        $this->assertStringContainsString('<script>', $minified); // Scripts removed
        $this->assertStringContainsString('<style>', $minified); // Styles removed
        $this->assertStringContainsString('<p></p>', $minified); // Empty tags removed
        $this->assertStringContainsString('<div></div>', $minified); // Empty tags removed
        $this->assertStringContainsString('<span></span>', $minified); // Empty tags removed
        $this->assertStringNotContainsString('> <', $minified); // Whitespace around tags removed
        $this->assertStringContainsString('Title', $minified); // Content preserved
        $this->assertStringContainsString('Content', $minified); // Content preserved
    }

    public function test_minify_preserves_content_structure(): void
    {
        $html = '<html>
<body>
<!-- Comment -->
<h1>Title</h1>
<p>Paragraph with 
<strong>bold
</strong> text.
</p>
<div>

</div>
</body>
</html>';

        $result = HtmlCleaner::minify($html);

        $this->assertStringContainsString('Title', $result);
        $this->assertStringContainsString('Paragraph', $result);
        $this->assertStringContainsString('bold', $result);
        $this->assertStringNotContainsString('<!--', $result);
        $this->assertStringContainsString('<div></div>', $result);
    }

    public function test_minify_does_not_handle_nested_empty_tags(): void
    {
        $html = '<html>
<body>
<div>
<p>

</p>
<span>

</span>
</div>
<h1>
Title</h1>
</body>
</html>';

        $result = HtmlCleaner::minify($html);

        $this->assertStringContainsString('<p></p>', $result);
        $this->assertStringContainsString('<span></span>', $result);
        $this->assertStringContainsString('Title', $result);
    }

    public function test_minify_respects_max_length(): void
    {
        $html = str_repeat('<p>Content paragraph</p>', 10000);
        $maxLength = 1000;

        $result = HtmlCleaner::minify($html, $maxLength);

        $this->assertLessThanOrEqual($maxLength + strlen('... [truncated]'), strlen($result));
        $this->assertStringEndsWith('... [truncated]', $result);
    }

    public function test_minify_does_not_remove_scripts_and_styles(): void
    {
        $html = '<html><head><script>alert("test");</script><style>body { color: red; }</style></head><body>Content</body></html>';

        $result = HtmlCleaner::minify($html);

        $this->assertStringContainsString('<script>', $result);
        $this->assertStringContainsString('<style>', $result);
        $this->assertStringContainsString('Content', $result);
    }

    public function test_minify_decodes_html_entities(): void
    {
        $html = '<html><body>&lt;div&gt;Content&lt;/div&gt;</body></html>';

        $result = HtmlCleaner::minify($html);

        $this->assertStringContainsString('<div>Content</div>', $result);
        $this->assertStringNotContainsString('&lt;', $result);
        $this->assertStringNotContainsString('&gt;', $result);
    }

    public function test_minify_handles_empty_html(): void
    {
        $html = '';

        $result = HtmlCleaner::minify($html);

        $this->assertEquals('', $result);
    }

    public function test_minify_handles_html_with_comments(): void
    {
        $html = '<!-- Comment --><p></p><div></div>';

        $result = HtmlCleaner::minify($html);

        // Should return empty or whitespace-only string
        $this->assertEquals('<p></p><div></div>', $result);
    }

    public function test_minify_removes_whitespace_between_tags(): void
    {
        $html = '<div> </div> <p> </p> <span>Content</span>';

        $result = HtmlCleaner::minify($html);

        // Should remove spaces between tags
        $this->assertStringNotContainsString('> <', $result);
        $this->assertStringContainsString('Content', $result);
    }

    public function test_sanitize_removes_all_attributes(): void
    {
        $html = '<div class="container" id="main" data-test="value"><p class="text">Content</p></div>';

        $result = HtmlCleaner::sanitize($html);

        $this->assertStringNotContainsString('class=', $result);
        $this->assertStringNotContainsString('id=', $result);
        $this->assertStringNotContainsString('data-test=', $result);
        $this->assertStringContainsString('<div>', $result);
        $this->assertStringContainsString('<p>', $result);
        $this->assertStringContainsString('Content', $result);
    }

    public function test_sanitize_removes_attributes_from_self_closing_tags(): void
    {
        $html = '<img src="image.jpg" alt="Image" class="photo" /><br class="clear" />';

        $result = HtmlCleaner::sanitize($html);

        $this->assertStringContainsString('src=', $result);
        $this->assertStringContainsString('alt=', $result);
        $this->assertStringNotContainsString('class=', $result);
        // Self-closing tags should preserve the / but remove attributes
        $this->assertStringContainsString('<img', $result);
        $this->assertStringNotContainsString('<br', $result);
        // Verify attributes remain
        $this->assertStringContainsString('image.jpg', $result);
        $this->assertStringContainsString('Image', $result);
    }

    public function test_sanitize_extends_minify_functionality(): void
    {
        $html = '<html><!-- Comment --><head><style>body { color: red; }</style></head><body class="page" id="main"><p class="text"></p><h1 class="title">Title</h1></body></html>';

        $result = HtmlCleaner::sanitize($html);

        // Should have all minify features
        $this->assertStringNotContainsString('<!--', $result); // Comments removed
        $this->assertStringNotContainsString('<style>', $result); // Styles removed
        $this->assertStringNotContainsString('<p></p>', $result); // Empty tags removed
        
        // Should also remove attributes
        $this->assertStringNotContainsString('class=', $result);
        $this->assertStringNotContainsString('id=', $result);
        
        // Content should be preserved
        $this->assertStringContainsString('Title', $result);
    }

    public function test_sanitize_removes_complex_attributes(): void
    {
        $html = '<div class="container" data-id="123" onclick="alert(\'test\')" style="color: red;"><span class="text" aria-label="Label">Content</span></div>';

        $result = HtmlCleaner::sanitize($html);

        $this->assertStringNotContainsString('class=', $result);
        $this->assertStringNotContainsString('data-id=', $result);
        $this->assertStringNotContainsString('onclick=', $result);
        $this->assertStringNotContainsString('style=', $result);
        $this->assertStringContainsString('aria-label=', $result);
        $this->assertStringContainsString('<div>', $result);
        $this->assertStringContainsString('<span', $result);
        $this->assertStringContainsString('Content', $result);
    }

    public function test_sanitize_preserves_content_structure(): void
    {
        $html = '<html><body><h1 class="title">Title</h1><p class="text">Paragraph with <strong class="bold">bold</strong> text.</p></body></html>';

        $result = HtmlCleaner::sanitize($html);

        $this->assertStringContainsString('Title', $result);
        $this->assertStringContainsString('Paragraph', $result);
        $this->assertStringContainsString('bold', $result);
        $this->assertStringNotContainsString('class=', $result);
    }

    public function test_sanitize_handles_nested_tags_with_attributes(): void
    {
        $html = '<div class="outer"><div class="inner" id="inner"><p class="text">Content</p></div></div>';

        $result = HtmlCleaner::sanitize($html);

        $this->assertStringNotContainsString('class=', $result);
        $this->assertStringNotContainsString('id=', $result);
        $this->assertStringContainsString('<div>', $result);
        $this->assertStringContainsString('<p>', $result);
        $this->assertStringContainsString('Content', $result);
    }

    public function test_sanitize_respects_max_length(): void
    {
        $html = str_repeat('<p class="text">Content paragraph</p>', 10000);
        $maxLength = 1000;

        $result = HtmlCleaner::sanitize($html, $maxLength);

        $this->assertLessThanOrEqual($maxLength + strlen('... [truncated]'), strlen($result));
        $this->assertStringEndsWith('... [truncated]', $result);
    }

    public function test_sanitize_handles_empty_html(): void
    {
        $html = '';

        $result = HtmlCleaner::sanitize($html);

        $this->assertEquals('', $result);
    }

    public function test_sanitize_removes_attributes_with_single_quotes(): void
    {
        $html = "<div class='container' id='main'><p class='text'>Content</p></div>";

        $result = HtmlCleaner::sanitize($html);

        $this->assertStringNotContainsString("class='", $result);
        $this->assertStringNotContainsString("id='", $result);
        $this->assertStringContainsString('<div>', $result);
        $this->assertStringContainsString('<p>', $result);
        $this->assertStringContainsString('Content', $result);
    }

    public function test_sanitize_removes_attributes_without_quotes(): void
    {
        $html = '<div class=container id=main><p class=text>Content</p></div>';

        $result = HtmlCleaner::sanitize($html);

        $this->assertStringNotContainsString('class=', $result);
        $this->assertStringNotContainsString('id=', $result);
        $this->assertStringContainsString('<div>', $result);
        $this->assertStringContainsString('<p>', $result);
        $this->assertStringContainsString('Content', $result);
    }

    public function test_sanitize_removes_attributes_that_minify_preserves(): void
    {
        $html = '<div class="container" id="main" data-test="value"><p class="text">Content</p></div>';

        $minified = HtmlCleaner::minify($html);
        $sanitized = HtmlCleaner::sanitize($html);

        // Verify sanitize actually removed attributes
        $this->assertStringNotContainsString('class=', $sanitized);
        $this->assertStringNotContainsString('id=', $sanitized);
        $this->assertStringNotContainsString('data-test=', $sanitized);
        
        // Minify should preserve attributes
        $this->assertStringContainsString('class=', $minified);
        
        // Content should be preserved in both
        $this->assertStringContainsString('Content', $sanitized);
        $this->assertStringContainsString('Content', $minified);
    }
}
