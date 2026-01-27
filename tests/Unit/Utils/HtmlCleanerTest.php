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

        $result = HtmlCleaner::clean($html);

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
}
