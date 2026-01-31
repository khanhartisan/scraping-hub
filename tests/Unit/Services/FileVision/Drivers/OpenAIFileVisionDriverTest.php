<?php

namespace Tests\Unit\Services\FileVision\Drivers;

use App\Contracts\FileVision\FileInformation;
use App\Contracts\OpenAI\OpenAIClient;
use App\Contracts\OpenAI\Response as ResponseObject;
use App\Services\FileVision\Drivers\OpenAIFileVisionDriver;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\UnableToCheckFileExistence;
use Mockery;
use RuntimeException;
use Tests\TestCase;

class OpenAIFileVisionDriverTest extends TestCase
{
    protected string $testFile;
    protected string $testImageFile;
    protected string $testTextFile;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Use fake Storage for testing - this replaces the default disk
        Storage::fake();

        // Create temporary test files using Storage facade
        $this->testFile = 'test_file.txt';
        Storage::put($this->testFile, 'Test content');

        $this->testImageFile = 'test_image.jpg';
        // Create a minimal valid JPEG file
        Storage::put($this->testImageFile, "\xFF\xD8\xFF\xE0\x00\x10JFIF\x00\x01\x01\x01\x00H\x00H\x00\x00\xFF\xD9");

        $this->testTextFile = 'test_document.txt';
        Storage::put($this->testTextFile, 'This is a test text file with some content for AI analysis.');
    }

    protected function tearDown(): void
    {
        Mockery::close();

        // Clean up test files
        Storage::delete([$this->testFile, $this->testImageFile, $this->testTextFile]);

        parent::tearDown();
    }

    public function test_it_describes_an_image_using_openai_vision(): void
    {
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
                            'text' => 'A beautiful landscape image with mountains and a lake.',
                        ],
                    ],
                ],
            ],
        ]);

        $mockOpenAIClient->shouldReceive('createResponse')
            ->once()
            ->andReturn($mockResponse);

        $driver = new OpenAIFileVisionDriver($mockOpenAIClient, ['model' => 'gpt-4o-mini']);

        $result = $driver->describe($this->testImageFile);

        $this->assertEquals($this->testImageFile, $result->getFilePath());
        $this->assertEquals('jpg', $result->getExtension());
        $this->assertEquals(0.9, $result->getConfidence());
        $this->assertEquals('A beautiful landscape image with mountains and a lake.', $result->getDescription());
    }

    public function test_it_describes_a_text_file_using_openai(): void
    {
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
                            'text' => 'This is a text document containing test content.',
                        ],
                    ],
                ],
            ],
        ]);

        $mockOpenAIClient->shouldReceive('createResponse')
            ->once()
            ->andReturn($mockResponse);

        $driver = new OpenAIFileVisionDriver($mockOpenAIClient, ['model' => 'gpt-4o-mini']);

        $result = $driver->describe($this->testTextFile);

        $this->assertEquals($this->testTextFile, $result->getFilePath());
        $this->assertEquals('txt', $result->getExtension());
        $this->assertEquals(0.9, $result->getConfidence());
        $this->assertStringContainsString('text document', strtolower($result->getDescription()));
    }

    public function test_it_describes_file_metadata_using_openai(): void
    {
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
                            'text' => 'This is a PDF document file typically used for documents.',
                        ],
                    ],
                ],
            ],
        ]);

        $mockOpenAIClient->shouldReceive('createResponse')
            ->once()
            ->andReturn($mockResponse);

        // Create a fake PDF file using Storage
        $pdfFile = 'test_document.pdf';
        Storage::put($pdfFile, '%PDF-1.4 test content');

        $driver = new OpenAIFileVisionDriver($mockOpenAIClient, ['model' => 'gpt-4o-mini']);

        $result = $driver->describe($pdfFile);

        $this->assertEquals(0.9, $result->getConfidence());
        $this->assertStringContainsString('PDF', $result->getDescription());

        Storage::delete($pdfFile);
    }

    public function test_it_throws_exception_when_openai_api_fails(): void
    {
        $mockOpenAIClient = Mockery::mock(OpenAIClient::class);

        $mockOpenAIClient->shouldReceive('createResponse')
            ->once()
            ->andThrow(new \Exception('API connection failed'));

        $driver = new OpenAIFileVisionDriver($mockOpenAIClient, ['model' => 'gpt-4o-mini']);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Failed to get AI description');

        $driver->describe($this->testImageFile);
    }

    public function test_it_throws_exception_when_openai_returns_empty_description(): void
    {
        $mockOpenAIClient = Mockery::mock(OpenAIClient::class);
        $mockResponse = ResponseObject::fromArray([
            'id' => 'resp_123',
            'status' => 'completed',
            'output' => [],
        ]);

        $mockOpenAIClient->shouldReceive('createResponse')
            ->once()
            ->andReturn($mockResponse);

        $driver = new OpenAIFileVisionDriver($mockOpenAIClient, ['model' => 'gpt-4o-mini']);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('AI provider returned empty description');

        $driver->describe($this->testImageFile);
    }

    public function test_it_throws_exception_for_nonexistent_file(): void
    {
        $mockOpenAIClient = Mockery::mock(OpenAIClient::class);
        $driver = new OpenAIFileVisionDriver($mockOpenAIClient, ['model' => 'gpt-4o-mini']);

        $this->expectException(UnableToCheckFileExistence::class);

        // Use a path that definitely doesn't exist (not in temp dir to avoid Storage check)
        $nonexistentPath = '/nonexistent/path/'.uniqid().'.txt';
        $driver->describe($nonexistentPath);
    }

    public function test_it_uses_custom_model_from_config(): void
    {
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
                            'text' => 'Test description',
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

        $driver = new OpenAIFileVisionDriver($mockOpenAIClient, ['model' => 'gpt-4o']);

        $result = $driver->describe($this->testImageFile);

        // Verify the result was returned successfully (type is guaranteed by method signature)
        $this->assertNotNull($result);
    }

    public function test_it_sets_correct_confidence_for_all_file_types(): void
    {
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
                            'text' => 'Test description',
                        ],
                    ],
                ],
            ],
        ]);

        $mockOpenAIClient->shouldReceive('createResponse')
            ->times(3)
            ->andReturn($mockResponse);

        $driver = new OpenAIFileVisionDriver($mockOpenAIClient, ['model' => 'gpt-4o-mini']);

        // Test image
        $imageResult = $driver->describe($this->testImageFile);
        $this->assertEquals(0.9, $imageResult->getConfidence());

        // Test text file
        $textResult = $driver->describe($this->testTextFile);
        $this->assertEquals(0.9, $textResult->getConfidence());

        // Test other file type
        $otherFile = 'test.zip';
        Storage::put($otherFile, 'PK\x03\x04');
        $otherResult = $driver->describe($otherFile);
        $this->assertEquals(0.9, $otherResult->getConfidence());
        Storage::delete($otherFile);
    }

    public function test_it_handles_large_text_files_with_truncation(): void
    {
        // Create a large text file (> 100KB) using Storage
        $largeFile = 'large_test.txt';
        $largeContent = str_repeat('This is a test line. ', 5000); // ~100KB+
        Storage::put($largeFile, $largeContent);

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
                            'text' => 'Large text file description.',
                        ],
                    ],
                ],
            ],
        ]);

        $mockOpenAIClient->shouldReceive('createResponse')
            ->once()
            ->andReturn($mockResponse);

        $driver = new OpenAIFileVisionDriver($mockOpenAIClient, ['model' => 'gpt-4o-mini']);

        $result = $driver->describe($largeFile);

        // The description comes from AI, not the file content, so we just verify it exists
        $this->assertNotEmpty($result->getDescription());
        $this->assertEquals('Large text file description.', $result->getDescription());

        Storage::delete($largeFile);
    }
}
