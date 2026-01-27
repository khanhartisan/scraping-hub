<?php

namespace Tests\Unit\Contracts\PageParser;

use App\Contracts\PageParser\PageData;
use Carbon\Carbon;
use Tests\TestCase;

class PageDataTest extends TestCase
{
    public function test_it_has_empty_linked_page_urls_by_default(): void
    {
        $pageData = new PageData();

        $this->assertIsArray($pageData->getLinkedPageUrls());
        $this->assertEmpty($pageData->getLinkedPageUrls());
    }

    public function test_it_can_set_and_get_linked_page_urls(): void
    {
        $pageData = new PageData();
        $urls = [
            'https://example.com/page1',
            'https://example.com/page2',
            '/relative/path',
        ];

        $pageData->setLinkedPageUrls($urls);

        $this->assertEquals($urls, $pageData->getLinkedPageUrls());
        $this->assertCount(3, $pageData->getLinkedPageUrls());
    }

    public function test_it_includes_linked_page_urls_in_to_array(): void
    {
        $pageData = new PageData();
        $pageData->setTitle('Test Title')
            ->setExcerpt('Test excerpt')
            ->setLinkedPageUrls([
                'https://example.com/page1',
                'https://example.com/page2',
            ]);

        $array = $pageData->toArray();

        $this->assertArrayHasKey('linked_page_urls', $array);
        $this->assertEquals([
            'https://example.com/page1',
            'https://example.com/page2',
        ], $array['linked_page_urls']);
    }

    public function test_it_can_create_from_array_with_linked_page_urls(): void
    {
        $data = [
            'title' => 'Test Title',
            'excerpt' => 'Test excerpt',
            'thumbnail_url' => 'https://example.com/image.jpg',
            'markdown_content' => '# Test',
            'linked_page_urls' => [
                'https://example.com/page1',
                'https://example.com/page2',
            ],
        ];

        $pageData = PageData::fromArray($data);

        $this->assertEquals('Test Title', $pageData->getTitle());
        $this->assertEquals([
            'https://example.com/page1',
            'https://example.com/page2',
        ], $pageData->getLinkedPageUrls());
    }

    public function test_it_handles_missing_linked_page_urls_in_from_array(): void
    {
        $data = [
            'title' => 'Test Title',
            'excerpt' => 'Test excerpt',
        ];

        $pageData = PageData::fromArray($data);

        $this->assertIsArray($pageData->getLinkedPageUrls());
        $this->assertEmpty($pageData->getLinkedPageUrls());
    }

    public function test_it_handles_empty_linked_page_urls_array(): void
    {
        $pageData = new PageData();
        $pageData->setLinkedPageUrls([]);

        $this->assertEmpty($pageData->getLinkedPageUrls());
        $this->assertArrayHasKey('linked_page_urls', $pageData->toArray());
        $this->assertEmpty($pageData->toArray()['linked_page_urls']);
    }

    public function test_it_preserves_linked_page_urls_in_serialization_roundtrip(): void
    {
        $originalUrls = [
            'https://example.com/page1',
            'https://example.com/page2',
            '/relative/path',
        ];

        $pageData = new PageData();
        $pageData->setTitle('Test')
            ->setLinkedPageUrls($originalUrls);

        $array = $pageData->toArray();
        $restored = PageData::fromArray($array);

        $this->assertEquals($originalUrls, $restored->getLinkedPageUrls());
    }

    public function test_it_handles_non_array_linked_page_urls_gracefully(): void
    {
        $data = [
            'title' => 'Test Title',
            'linked_page_urls' => 'not-an-array',
        ];

        $pageData = PageData::fromArray($data);

        // Should default to empty array when invalid type is provided
        $this->assertIsArray($pageData->getLinkedPageUrls());
        $this->assertEmpty($pageData->getLinkedPageUrls());
    }
}
