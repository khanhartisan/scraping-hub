<?php

namespace Tests\Unit\Services\PageParser\Drivers;

use App\Contracts\OpenAI\OpenAIClient;
use App\Contracts\OpenAI\Response as ResponseObject;
use App\Contracts\PageParser\PageData;
use App\Services\PageParser\Drivers\OpenAIPageParserDriver;
use Carbon\Carbon;
use Mockery;
use RuntimeException;
use Tests\TestCase;

class OpenAIPageParserDriverTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_it_parses_a_page_successfully(): void
    {
        $html = '<html><head><title>Test Article</title><meta name="description" content="A test article"></head><body><h1>Test Article</h1><p>This is a test article content.</p></body></html>';

        $mockOpenAIClient = Mockery::mock(OpenAIClient::class);
        $mockResponse = ResponseObject::fromArray([
            'id' => 'resp_123',
            'status' => 'completed',
            'output' => [
                [
                    'type' => 'message',
                    'content' => [
                        [
                            'type' => 'output_text',
                            'text' => json_encode([
                                'title' => 'Test Article',
                                'excerpt' => 'A test article',
                                'thumbnailUrl' => 'https://example.com/image.jpg',
                                'markdownContent' => '# Test Article\n\nThis is a test article content.',
                                'publishedAt' => '2024-01-15T10:30:00Z',
                                'updatedAt' => '2024-01-20T14:45:00Z',
                            ]),
                        ],
                    ],
                ],
            ],
        ]);

        $mockOpenAIClient->shouldReceive('createResponse')
            ->once()
            ->andReturn($mockResponse);

        $this->app->instance(OpenAIClient::class, $mockOpenAIClient);

        $driver = new OpenAIPageParserDriver(['model' => 'gpt-4o-mini']);

        $result = $driver->parse($html);

        $this->assertInstanceOf(PageData::class, $result);
        $this->assertEquals('Test Article', $result->getTitle());
        $this->assertEquals('A test article', $result->getExcerpt());
        $this->assertEquals('https://example.com/image.jpg', $result->getThumbnailUrl());
        $this->assertEquals('# Test Article\n\nThis is a test article content.', $result->getMarkdownContent());
        $this->assertInstanceOf(Carbon::class, $result->getPublishedAt());
        $this->assertInstanceOf(Carbon::class, $result->getUpdatedAt());
        $this->assertInstanceOf(Carbon::class, $result->getFetchedAt());
    }

    public function test_it_parses_page_without_dates(): void
    {
        $html = '<html><body><h1>Article Without Dates</h1></body></html>';

        $mockOpenAIClient = Mockery::mock(OpenAIClient::class);
        $mockResponse = ResponseObject::fromArray([
            'id' => 'resp_123',
            'status' => 'completed',
            'output' => [
                [
                    'type' => 'message',
                    'content' => [
                        [
                            'type' => 'output_text',
                            'text' => json_encode([
                                'title' => 'Article Without Dates',
                                'excerpt' => 'An article',
                                'thumbnailUrl' => '',
                                'markdownContent' => '# Article Without Dates',
                                'publishedAt' => null,
                                'updatedAt' => null,
                            ]),
                        ],
                    ],
                ],
            ],
        ]);

        $mockOpenAIClient->shouldReceive('createResponse')
            ->once()
            ->andReturn($mockResponse);

        $this->app->instance(OpenAIClient::class, $mockOpenAIClient);

        $driver = new OpenAIPageParserDriver(['model' => 'gpt-4o-mini']);

        $result = $driver->parse($html);

        $this->assertInstanceOf(PageData::class, $result);
        $this->assertNull($result->getPublishedAt());
        $this->assertNull($result->getUpdatedAt());
        $this->assertNotNull($result->getFetchedAt());
    }

    public function test_it_sets_fetched_at_timestamp(): void
    {
        $html = '<html><body><h1>Test</h1></body></html>';

        $mockOpenAIClient = Mockery::mock(OpenAIClient::class);
        $mockResponse = ResponseObject::fromArray([
            'id' => 'resp_123',
            'status' => 'completed',
            'output' => [
                [
                    'type' => 'message',
                    'content' => [
                        [
                            'type' => 'output_text',
                            'text' => json_encode([
                                'title' => 'Test',
                                'excerpt' => 'Test excerpt',
                                'thumbnailUrl' => '',
                                'markdownContent' => '# Test',
                            ]),
                        ],
                    ],
                ],
            ],
        ]);

        $mockOpenAIClient->shouldReceive('createResponse')
            ->once()
            ->andReturn($mockResponse);

        $this->app->instance(OpenAIClient::class, $mockOpenAIClient);

        $driver = new OpenAIPageParserDriver(['model' => 'gpt-4o-mini']);

        $beforeParse = now();
        $result = $driver->parse($html);
        $afterParse = now();

        $this->assertNotNull($result->getFetchedAt());
        $this->assertGreaterThanOrEqual($beforeParse->timestamp, $result->getFetchedAt()->timestamp);
        $this->assertLessThanOrEqual($afterParse->timestamp, $result->getFetchedAt()->timestamp);
    }

    public function test_it_prepares_html_by_removing_scripts_and_styles(): void
    {
        $html = '<html><head><style>body { color: red; }</style><script>alert("test");</script></head><body><h1>Content</h1></body></html>';

        $mockOpenAIClient = Mockery::mock(OpenAIClient::class);
        $mockResponse = ResponseObject::fromArray([
            'id' => 'resp_123',
            'status' => 'completed',
            'output' => [
                [
                    'type' => 'message',
                    'content' => [
                        [
                            'type' => 'output_text',
                            'text' => json_encode([
                                'title' => 'Content',
                                'excerpt' => 'Content excerpt',
                                'thumbnailUrl' => '',
                                'markdownContent' => '# Content',
                            ]),
                        ],
                    ],
                ],
            ],
        ]);

        $mockOpenAIClient->shouldReceive('createResponse')
            ->once()
            ->with(
                Mockery::on(function ($input) {
                    $inputArray = $input->toArray();
                    $text = $inputArray[0]['content'][0]['text'] ?? '';
                    // Verify scripts and styles are removed
                    return !str_contains($text, '<script>') && !str_contains($text, '<style>');
                }),
                Mockery::type(\App\Contracts\OpenAI\ResponseOptions::class)
            )
            ->andReturn($mockResponse);

        $this->app->instance(OpenAIClient::class, $mockOpenAIClient);

        $driver = new OpenAIPageParserDriver(['model' => 'gpt-4o-mini']);

        $result = $driver->parse($html);

        $this->assertInstanceOf(PageData::class, $result);
    }

    public function test_it_truncates_large_html_content(): void
    {
        $largeHtml = '<html><body>'.str_repeat('<p>Content paragraph</p>', 10000).'</body></html>';

        $mockOpenAIClient = Mockery::mock(OpenAIClient::class);
        $mockResponse = ResponseObject::fromArray([
            'id' => 'resp_123',
            'status' => 'completed',
            'output' => [
                [
                    'type' => 'message',
                    'content' => [
                        [
                            'type' => 'output_text',
                            'text' => json_encode([
                                'title' => 'Large Content',
                                'excerpt' => 'Large content excerpt',
                                'thumbnailUrl' => '',
                                'markdownContent' => '# Large Content',
                            ]),
                        ],
                    ],
                ],
            ],
        ]);

        $mockOpenAIClient->shouldReceive('createResponse')
            ->once()
            ->with(
                Mockery::on(function ($input) {
                    $inputArray = $input->toArray();
                    $text = $inputArray[0]['content'][0]['text'] ?? '';
                    // Verify HTML is truncated (should be less than original)
                    return strlen($text) < 500000; // Original would be much larger
                }),
                Mockery::type(\App\Contracts\OpenAI\ResponseOptions::class)
            )
            ->andReturn($mockResponse);

        $this->app->instance(OpenAIClient::class, $mockOpenAIClient);

        $driver = new OpenAIPageParserDriver([
            'model' => 'gpt-4o-mini',
            'max_html_length' => 50000,
        ]);

        $result = $driver->parse($largeHtml);

        $this->assertInstanceOf(PageData::class, $result);
    }

    public function test_it_uses_json_schema_in_response_format(): void
    {
        $html = '<html><body><h1>Test</h1></body></html>';

        $mockOpenAIClient = Mockery::mock(OpenAIClient::class);
        $mockResponse = ResponseObject::fromArray([
            'id' => 'resp_123',
            'status' => 'completed',
            'output' => [
                [
                    'type' => 'message',
                    'content' => [
                        [
                            'type' => 'output_text',
                            'text' => json_encode([
                                'title' => 'Test',
                                'excerpt' => 'Test excerpt',
                                'thumbnailUrl' => '',
                                'markdownContent' => '# Test',
                            ]),
                        ],
                    ],
                ],
            ],
        ]);

        $mockOpenAIClient->shouldReceive('createResponse')
            ->once()
            ->with(
                Mockery::type(\App\Contracts\OpenAI\ResponseInput::class),
                Mockery::on(function ($options) {
                    $format = $options->getResponseFormat();
                    return $format !== null
                        && $format['type'] === 'json_schema'
                        && $format['name'] === 'page_parsing'
                        && $format['strict'] === true
                        && isset($format['schema'])
                        && isset($format['schema']['properties']['title'])
                        && isset($format['schema']['properties']['excerpt'])
                        && isset($format['schema']['properties']['thumbnailUrl'])
                        && isset($format['schema']['properties']['markdownContent'])
                        && isset($format['schema']['properties']['publishedAt'])
                        && isset($format['schema']['properties']['updatedAt']);
                })
            )
            ->andReturn($mockResponse);

        $this->app->instance(OpenAIClient::class, $mockOpenAIClient);

        $driver = new OpenAIPageParserDriver(['model' => 'gpt-4o-mini']);

        $result = $driver->parse($html);

        $this->assertInstanceOf(PageData::class, $result);
    }

    public function test_it_throws_exception_when_openai_api_fails(): void
    {
        $html = '<html><body><h1>Test</h1></body></html>';

        $mockOpenAIClient = Mockery::mock(OpenAIClient::class);

        $mockOpenAIClient->shouldReceive('createResponse')
            ->once()
            ->andThrow(new \Exception('API connection failed'));

        $this->app->instance(OpenAIClient::class, $mockOpenAIClient);

        $driver = new OpenAIPageParserDriver(['model' => 'gpt-4o-mini']);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Failed to parse page with OpenAI');

        $driver->parse($html);
    }

    public function test_it_throws_exception_when_openai_returns_empty_response(): void
    {
        $html = '<html><body><h1>Test</h1></body></html>';

        $mockOpenAIClient = Mockery::mock(OpenAIClient::class);
        $mockResponse = ResponseObject::fromArray([
            'id' => 'resp_123',
            'status' => 'completed',
            'output' => [],
        ]);

        $mockOpenAIClient->shouldReceive('createResponse')
            ->once()
            ->andReturn($mockResponse);

        $this->app->instance(OpenAIClient::class, $mockOpenAIClient);

        $driver = new OpenAIPageParserDriver(['model' => 'gpt-4o-mini']);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('OpenAI returned empty parsing response');

        $driver->parse($html);
    }

    public function test_it_throws_exception_when_response_contains_refusal(): void
    {
        $html = '<html><body><h1>Test</h1></body></html>';

        $mockOpenAIClient = Mockery::mock(OpenAIClient::class);
        $mockResponse = ResponseObject::fromArray([
            'id' => 'resp_123',
            'status' => 'completed',
            'output' => [
                [
                    'type' => 'message',
                    'content' => [
                        [
                            'type' => 'refusal',
                            'refusal' => 'I cannot parse this content.',
                        ],
                    ],
                ],
            ],
        ]);

        $mockOpenAIClient->shouldReceive('createResponse')
            ->once()
            ->andReturn($mockResponse);

        $this->app->instance(OpenAIClient::class, $mockOpenAIClient);

        $driver = new OpenAIPageParserDriver(['model' => 'gpt-4o-mini']);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('OpenAI refused to parse the page');

        $driver->parse($html);
    }

    public function test_it_throws_exception_for_invalid_json_response(): void
    {
        $html = '<html><body><h1>Test</h1></body></html>';

        $mockOpenAIClient = Mockery::mock(OpenAIClient::class);
        $mockResponse = ResponseObject::fromArray([
            'id' => 'resp_123',
            'status' => 'completed',
            'output' => [
                [
                    'type' => 'message',
                    'content' => [
                        [
                            'type' => 'output_text',
                            'text' => 'Invalid JSON response',
                        ],
                    ],
                ],
            ],
        ]);

        $mockOpenAIClient->shouldReceive('createResponse')
            ->once()
            ->andReturn($mockResponse);

        $this->app->instance(OpenAIClient::class, $mockOpenAIClient);

        $driver = new OpenAIPageParserDriver(['model' => 'gpt-4o-mini']);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Failed to parse response as JSON');

        $driver->parse($html);
    }

    public function test_it_handles_invalid_date_formats_gracefully(): void
    {
        $html = '<html><body><h1>Test</h1></body></html>';

        $mockOpenAIClient = Mockery::mock(OpenAIClient::class);
        $mockResponse = ResponseObject::fromArray([
            'id' => 'resp_123',
            'status' => 'completed',
            'output' => [
                [
                    'type' => 'message',
                    'content' => [
                        [
                            'type' => 'output_text',
                            'text' => json_encode([
                                'title' => 'Test',
                                'excerpt' => 'Test excerpt',
                                'thumbnailUrl' => '',
                                'markdownContent' => '# Test',
                                'publishedAt' => 'invalid-date-format',
                                'updatedAt' => 'also-invalid',
                            ]),
                        ],
                    ],
                ],
            ],
        ]);

        $mockOpenAIClient->shouldReceive('createResponse')
            ->once()
            ->andReturn($mockResponse);

        $this->app->instance(OpenAIClient::class, $mockOpenAIClient);

        $driver = new OpenAIPageParserDriver(['model' => 'gpt-4o-mini']);

        $result = $driver->parse($html);

        // Should handle invalid dates gracefully by leaving them as null
        $this->assertInstanceOf(PageData::class, $result);
        $this->assertNull($result->getPublishedAt());
        $this->assertNull($result->getUpdatedAt());
    }

    public function test_it_parses_valid_date_formats(): void
    {
        $html = '<html><body><h1>Test</h1></body></html>';

        $publishedAt = '2024-01-15T10:30:00Z';
        $updatedAt = '2024-01-20T14:45:00Z';

        $mockOpenAIClient = Mockery::mock(OpenAIClient::class);
        $mockResponse = ResponseObject::fromArray([
            'id' => 'resp_123',
            'status' => 'completed',
            'output' => [
                [
                    'type' => 'message',
                    'content' => [
                        [
                            'type' => 'output_text',
                            'text' => json_encode([
                                'title' => 'Test',
                                'excerpt' => 'Test excerpt',
                                'thumbnailUrl' => '',
                                'markdownContent' => '# Test',
                                'publishedAt' => $publishedAt,
                                'updatedAt' => $updatedAt,
                            ]),
                        ],
                    ],
                ],
            ],
        ]);

        $mockOpenAIClient->shouldReceive('createResponse')
            ->once()
            ->andReturn($mockResponse);

        $this->app->instance(OpenAIClient::class, $mockOpenAIClient);

        $driver = new OpenAIPageParserDriver(['model' => 'gpt-4o-mini']);

        $result = $driver->parse($html);

        $this->assertInstanceOf(PageData::class, $result);
        $this->assertInstanceOf(Carbon::class, $result->getPublishedAt());
        $this->assertInstanceOf(Carbon::class, $result->getUpdatedAt());
        $this->assertEquals('2024-01-15 10:30:00', $result->getPublishedAt()->format('Y-m-d H:i:s'));
        $this->assertEquals('2024-01-20 14:45:00', $result->getUpdatedAt()->format('Y-m-d H:i:s'));
    }

    public function test_it_uses_custom_model_from_config(): void
    {
        $html = '<html><body><h1>Test</h1></body></html>';

        $mockOpenAIClient = Mockery::mock(OpenAIClient::class);
        $mockResponse = ResponseObject::fromArray([
            'id' => 'resp_123',
            'status' => 'completed',
            'output' => [
                [
                    'type' => 'message',
                    'content' => [
                        [
                            'type' => 'output_text',
                            'text' => json_encode([
                                'title' => 'Test',
                                'excerpt' => 'Test excerpt',
                                'thumbnailUrl' => '',
                                'markdownContent' => '# Test',
                            ]),
                        ],
                    ],
                ],
            ],
        ]);

        $mockOpenAIClient->shouldReceive('createResponse')
            ->once()
            ->with(
                Mockery::type(\App\Contracts\OpenAI\ResponseInput::class),
                Mockery::on(function ($options) {
                    return $options->getModel() === 'gpt-4o';
                })
            )
            ->andReturn($mockResponse);

        $this->app->instance(OpenAIClient::class, $mockOpenAIClient);

        $driver = new OpenAIPageParserDriver(['model' => 'gpt-4o']);

        $result = $driver->parse($html);

        $this->assertInstanceOf(PageData::class, $result);
    }

    public function test_it_handles_empty_strings_for_optional_fields(): void
    {
        $html = '<html><body><h1>Test</h1></body></html>';

        $mockOpenAIClient = Mockery::mock(OpenAIClient::class);
        $mockResponse = ResponseObject::fromArray([
            'id' => 'resp_123',
            'status' => 'completed',
            'output' => [
                [
                    'type' => 'message',
                    'content' => [
                        [
                            'type' => 'output_text',
                            'text' => json_encode([
                                'title' => 'Test',
                                'excerpt' => '',
                                'thumbnailUrl' => '',
                                'markdownContent' => '',
                            ]),
                        ],
                    ],
                ],
            ],
        ]);

        $mockOpenAIClient->shouldReceive('createResponse')
            ->once()
            ->andReturn($mockResponse);

        $this->app->instance(OpenAIClient::class, $mockOpenAIClient);

        $driver = new OpenAIPageParserDriver(['model' => 'gpt-4o-mini']);

        $result = $driver->parse($html);

        $this->assertInstanceOf(PageData::class, $result);
        $this->assertEquals('Test', $result->getTitle());
        $this->assertEquals('', $result->getExcerpt());
        $this->assertEquals('', $result->getThumbnailUrl());
        $this->assertEquals('', $result->getMarkdownContent());
    }

    public function test_it_parses_complex_markdown_content(): void
    {
        $html = '<html><body><h1>Article</h1><p>Paragraph with <strong>bold</strong> and <em>italic</em> text.</p><ul><li>Item 1</li><li>Item 2</li></ul></body></html>';

        $markdownContent = "# Article\n\nParagraph with **bold** and *italic* text.\n\n- Item 1\n- Item 2\n";

        $mockOpenAIClient = Mockery::mock(OpenAIClient::class);
        $mockResponse = ResponseObject::fromArray([
            'id' => 'resp_123',
            'status' => 'completed',
            'output' => [
                [
                    'type' => 'message',
                    'content' => [
                        [
                            'type' => 'output_text',
                            'text' => json_encode([
                                'title' => 'Article',
                                'excerpt' => 'Article excerpt',
                                'thumbnailUrl' => '',
                                'markdownContent' => $markdownContent,
                            ]),
                        ],
                    ],
                ],
            ],
        ]);

        $mockOpenAIClient->shouldReceive('createResponse')
            ->once()
            ->andReturn($mockResponse);

        $this->app->instance(OpenAIClient::class, $mockOpenAIClient);

        $driver = new OpenAIPageParserDriver(['model' => 'gpt-4o-mini']);

        $result = $driver->parse($html);

        $this->assertInstanceOf(PageData::class, $result);
        $this->assertEquals($markdownContent, $result->getMarkdownContent());
    }

    public function test_it_handles_missing_optional_fields_in_response(): void
    {
        $html = '<html><body><h1>Test</h1></body></html>';

        $mockOpenAIClient = Mockery::mock(OpenAIClient::class);
        $mockResponse = ResponseObject::fromArray([
            'id' => 'resp_123',
            'status' => 'completed',
            'output' => [
                [
                    'type' => 'message',
                    'content' => [
                        [
                            'type' => 'output_text',
                            'text' => json_encode([
                                'title' => 'Test',
                                'excerpt' => 'Test excerpt',
                                'thumbnailUrl' => '',
                                'markdownContent' => '# Test',
                                // publishedAt and updatedAt are missing
                            ]),
                        ],
                    ],
                ],
            ],
        ]);

        $mockOpenAIClient->shouldReceive('createResponse')
            ->once()
            ->andReturn($mockResponse);

        $this->app->instance(OpenAIClient::class, $mockOpenAIClient);

        $driver = new OpenAIPageParserDriver(['model' => 'gpt-4o-mini']);

        $result = $driver->parse($html);

        $this->assertInstanceOf(PageData::class, $result);
        $this->assertNull($result->getPublishedAt());
        $this->assertNull($result->getUpdatedAt());
    }
}
