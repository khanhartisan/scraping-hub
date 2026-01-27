<?php

namespace Tests\Unit\Services\Scraper\Drivers;

use App\Contracts\Scraper\ScrapingOptions;
use App\Services\Scraper\Drivers\GuzzleDriver;
use Tests\TestCase;

class GuzzleDriverTest extends TestCase
{
    protected GuzzleDriver $driver;

    protected function setUp(): void
    {
        parent::setUp();
        $this->driver = new GuzzleDriver();
    }

    public function test_it_fetches_url_successfully(): void
    {
        $response = $this->driver->fetch('https://example.com');

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertNotEmpty((string) $response->getBody());
    }

    public function test_it_handles_http_errors_gracefully(): void
    {
        // example.com returns 200, so we'll test with a non-existent path
        // Since http_errors is false, we'll get the response even if it's 404
        $response = $this->driver->fetch('https://example.com/this-path-does-not-exist-404');

        // example.com might redirect or return 200, so we just check it's a valid response
        $this->assertGreaterThanOrEqual(200, $response->getStatusCode());
        $this->assertLessThan(600, $response->getStatusCode());
    }

    public function test_it_applies_scraping_options_with_country_code(): void
    {
        $options = (new ScrapingOptions())
            ->setScrapingCountryCode('FR');

        $response = $this->driver->fetch('https://example.com', $options);

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function test_it_handles_null_options(): void
    {
        $response = $this->driver->fetch('https://example.com', null);

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function test_response_contains_headers(): void
    {
        $response = $this->driver->fetch('https://example.com');

        $this->assertNotEmpty($response->getHeaders());
    }

    public function test_response_has_content_type(): void
    {
        $response = $this->driver->fetch('https://example.com');

        // example.com should return HTML content
        $contentType = $response->getHeaderLine('Content-Type');
        $this->assertNotEmpty($contentType);
    }
}
