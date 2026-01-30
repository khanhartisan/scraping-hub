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

    public function test_it_has_empty_canonical_url_by_default(): void
    {
        $pageData = new PageData();

        $this->assertEquals('', $pageData->getCanonicalUrl());
    }

    public function test_it_can_set_and_get_canonical_url(): void
    {
        $pageData = new PageData();
        $canonicalUrl = 'https://example.com/canonical-page';

        $pageData->setCanonicalUrl($canonicalUrl);

        $this->assertEquals($canonicalUrl, $pageData->getCanonicalUrl());
    }

    public function test_it_defaults_canonical_number_to_null(): void
    {
        $pageData = new PageData();

        $this->assertNull($pageData->getCanonicalNumber());
    }

    public function test_it_can_set_and_get_canonical_number(): void
    {
        $pageData = new PageData();

        $pageData->setCanonicalNumber(2);
        $this->assertEquals(2, $pageData->getCanonicalNumber());

        $pageData->setCanonicalNumber(5);
        $this->assertEquals(5, $pageData->getCanonicalNumber());

        $pageData->setCanonicalNumber(null);
        $this->assertNull($pageData->getCanonicalNumber());
    }

    public function test_it_includes_canonical_fields_in_to_array(): void
    {
        $pageData = new PageData();
        $pageData->setTitle('Test Title')
            ->setCanonicalUrl('https://example.com/canonical')
            ->setCanonicalNumber(2);

        $array = $pageData->toArray();

        $this->assertArrayHasKey('canonical_url', $array);
        $this->assertArrayHasKey('canonical_number', $array);
        $this->assertEquals('https://example.com/canonical', $array['canonical_url']);
        $this->assertEquals(2, $array['canonical_number']);
    }

    public function test_it_can_create_from_array_with_canonical_fields(): void
    {
        $data = [
            'title' => 'Test Title',
            'excerpt' => 'Test excerpt',
            'canonical_url' => 'https://example.com/canonical',
            'canonical_number' => 3,
        ];

        $pageData = PageData::fromArray($data);

        $this->assertEquals('https://example.com/canonical', $pageData->getCanonicalUrl());
        $this->assertEquals(3, $pageData->getCanonicalNumber());
    }

    public function test_it_handles_missing_canonical_fields_in_from_array(): void
    {
        $data = [
            'title' => 'Test Title',
        ];

        $pageData = PageData::fromArray($data);

        // Should default to empty string and null
        $this->assertEquals('', $pageData->getCanonicalUrl());
        $this->assertNull($pageData->getCanonicalNumber());
    }

    public function test_it_preserves_canonical_fields_in_serialization_roundtrip(): void
    {
        $pageData = new PageData();
        $pageData->setTitle('Test')
            ->setCanonicalUrl('https://example.com/canonical')
            ->setCanonicalNumber(5);

        $array = $pageData->toArray();
        $restored = PageData::fromArray($array);

        $this->assertEquals('https://example.com/canonical', $restored->getCanonicalUrl());
        $this->assertEquals(5, $restored->getCanonicalNumber());
    }
}
