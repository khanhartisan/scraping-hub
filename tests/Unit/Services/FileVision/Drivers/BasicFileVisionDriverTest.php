<?php

namespace Tests\Unit\Services\FileVision\Drivers;

use App\Contracts\FileVision\FileInformation;
use App\Services\FileVision\Drivers\BasicFileVisionDriver;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\UnableToCheckFileExistence;
use Tests\TestCase;

class BasicFileVisionDriverTest extends TestCase
{
    protected string $testFile;
    protected string $testImageFile;
    protected string $testTextFile;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Use fake Storage for testing - this replaces the default disk
        Storage::fake();

        // Create temporary test files using Storage facade (uses default disk)
        $this->testFile = 'test_file.txt';
        Storage::put($this->testFile, 'Test content');

        $this->testImageFile = 'test_image.jpg';
        // Create a minimal valid JPEG file
        Storage::put($this->testImageFile, "\xFF\xD8\xFF\xE0\x00\x10JFIF\x00\x01\x01\x01\x00H\x00H\x00\x00\xFF\xD9");

        $this->testTextFile = 'test_document.txt';
        Storage::put($this->testTextFile, 'This is a test text file with some content.');
    }

    protected function tearDown(): void
    {
        // Clean up test files
        Storage::delete([$this->testFile, $this->testImageFile, $this->testTextFile]);

        parent::tearDown();
    }

    public function test_it_describes_a_text_file(): void
    {
        $driver = new BasicFileVisionDriver();

        $result = $driver->describe($this->testFile);

        $this->assertEquals($this->testFile, $result->getFilePath());
        $this->assertEquals('txt', $result->getExtension());
        $this->assertNotNull($result->getMimeType());
        $this->assertEquals(1.0, $result->getConfidence());
        $this->assertNotNull($result->getDescription());
        $this->assertStringContainsString('text file', strtolower($result->getDescription()));
        $this->assertStringContainsString('TXT', $result->getDescription());
    }

    public function test_it_describes_an_image_file(): void
    {
        $driver = new BasicFileVisionDriver();

        $result = $driver->describe($this->testImageFile);

        $this->assertEquals($this->testImageFile, $result->getFilePath());
        $this->assertEquals('jpg', $result->getExtension());
        $this->assertNotNull($result->getMimeType());
        $this->assertEquals(1.0, $result->getConfidence());
        $this->assertNotNull($result->getDescription());
        $this->assertStringContainsString('image', strtolower($result->getDescription()));
        $this->assertStringContainsString('JPG', $result->getDescription());
    }

    public function test_it_throws_exception_for_nonexistent_file(): void
    {
        $driver = new BasicFileVisionDriver();

        $this->expectException(UnableToCheckFileExistence::class);

        // Use a path that definitely doesn't exist (not in temp dir to avoid Storage check)
        $nonexistentPath = '/nonexistent/path/'.uniqid().'.txt';
        $driver->describe($nonexistentPath);
    }

    public function test_it_generates_basic_description(): void
    {
        $driver = new BasicFileVisionDriver();

        $description = $driver->generateDescriptionForFile(
            $this->testTextFile,
            'txt',
            'text/plain'
        );

        $this->assertIsString($description);
        $this->assertStringContainsString('text file', strtolower($description));
        $this->assertStringContainsString('TXT', $description);
        $this->assertStringContainsString('size', strtolower($description));
    }

    public function test_it_handles_different_file_types(): void
    {
        $driver = new BasicFileVisionDriver();

        // Create temporary files for testing using Storage
        $pdfFile = 'test_document.pdf';
        Storage::put($pdfFile, '%PDF-1.4 test');
        
        $jsonFile = 'test_data.json';
        Storage::put($jsonFile, '{"test": "data"}');
        
        $videoFile = 'test_video.mp4';
        Storage::put($videoFile, 'fake video content');

        try {
            // Test PDF type description
            $pdfDescription = $driver->generateDescriptionForFile(
                $pdfFile,
                'pdf',
                'application/pdf'
            );
            $this->assertStringContainsString('PDF document', $pdfDescription);

            // Test JSON type description
            $jsonDescription = $driver->generateDescriptionForFile(
                $jsonFile,
                'json',
                'application/json'
            );
            $this->assertStringContainsString('JSON data', $jsonDescription);

            // Test video type description
            $videoDescription = $driver->generateDescriptionForFile(
                $videoFile,
                'mp4',
                'video/mp4'
            );
            $this->assertStringContainsString('video', strtolower($videoDescription));
        } finally {
            // Clean up
            Storage::delete([$pdfFile, $jsonFile, $videoFile]);
        }
    }

    public function test_it_includes_file_size_in_description(): void
    {
        $driver = new BasicFileVisionDriver();

        $result = $driver->describe($this->testFile);

        $description = $result->getDescription();
        $this->assertStringContainsString('size', strtolower($description));
        // Should contain size units like B, KB, MB, etc.
        $this->assertMatchesRegularExpression('/\d+\.?\d*\s*(B|KB|MB|GB|TB)/', $description);
    }

    public function test_it_sets_correct_confidence(): void
    {
        $driver = new BasicFileVisionDriver();

        $result = $driver->describe($this->testFile);

        $this->assertEquals(1.0, $result->getConfidence());
    }
}
