<?php

namespace Tests\Unit\Contracts\OpenAI;

use App\Contracts\OpenAI\ResponseInput;
use Tests\TestCase;

class ResponseInputTest extends TestCase
{
    public function test_it_creates_text_only_input(): void
    {
        $input = ResponseInput::text('Hello world');

        $this->assertTrue($input->isTextOnly());
        $this->assertFalse($input->hasImages());
        
        $result = $input->toArray();
        $this->assertIsArray($result);
        $this->assertCount(1, $result);
        $this->assertEquals('user', $result[0]['role']);
        $this->assertCount(1, $result[0]['content']);
        $this->assertEquals('input_text', $result[0]['content'][0]['type']);
        $this->assertEquals('Hello world', $result[0]['content'][0]['text']);
    }

    public function test_it_creates_input_with_image(): void
    {
        $input = ResponseInput::withImage('What is this?', 'https://example.com/image.jpg');

        $this->assertFalse($input->isTextOnly());
        $this->assertTrue($input->hasImages());

        $result = $input->toArray();
        $this->assertIsArray($result);
        $this->assertCount(1, $result);
        $this->assertEquals('user', $result[0]['role']);
        $this->assertCount(2, $result[0]['content']);
        $this->assertEquals('input_text', $result[0]['content'][0]['type']);
        $this->assertEquals('input_image', $result[0]['content'][1]['type']);
    }

    public function test_it_adds_text_content(): void
    {
        $input = ResponseInput::text('First')
            ->addText('Second');

        $result = $input->toArray();
        $this->assertIsArray($result);
        $this->assertCount(2, $result[0]['content']);
        $this->assertEquals('input_text', $result[0]['content'][0]['type']);
        $this->assertEquals('First', $result[0]['content'][0]['text']);
        $this->assertEquals('input_text', $result[0]['content'][1]['type']);
        $this->assertEquals('Second', $result[0]['content'][1]['text']);
    }

    public function test_it_adds_image_url(): void
    {
        $input = ResponseInput::text('Look at this')
            ->addImage('https://example.com/image.jpg');

        $this->assertTrue($input->hasImages());

        $result = $input->toArray();
        $imageContent = $result[0]['content'][1];
        $this->assertEquals('input_image', $imageContent['type']);
        $this->assertEquals('https://example.com/image.jpg', $imageContent['image_url']);
    }

    public function test_it_adds_image_with_detail(): void
    {
        $input = ResponseInput::text('Analyze this')
            ->addImage('https://example.com/image.jpg', 'high');

        $result = $input->toArray();
        $imageContent = $result[0]['content'][1];
        $this->assertEquals('high', $imageContent['detail']);
    }

    public function test_it_adds_image_from_base64(): void
    {
        $base64 = base64_encode('fake-image-data');
        $input = ResponseInput::text('Check this')
            ->addImageFromBase64($base64, 'image/png');

        $result = $input->toArray();
        $imageContent = $result[0]['content'][1];
        $this->assertEquals('input_image', $imageContent['type']);
        $this->assertStringStartsWith('data:image/png;base64,', $imageContent['image_url']);
    }

    public function test_it_adds_image_from_file_id(): void
    {
        $input = ResponseInput::text('Review this')
            ->addImageFromFileId('file_123');

        $result = $input->toArray();
        $imageContent = $result[0]['content'][1];
        $this->assertEquals('input_image', $imageContent['type']);
        $this->assertEquals('file_123', $imageContent['file_id']);
    }

    public function test_it_creates_multi_message_conversation(): void
    {
        $input = ResponseInput::text('First message')
            ->newMessage()
            ->addText('Second message');

        $result = $input->toArray();
        $this->assertCount(2, $result);
        $this->assertEquals('user', $result[0]['role']);
        $this->assertEquals('user', $result[1]['role']);
        $this->assertEquals('First message', $result[0]['content'][0]['text']);
        $this->assertEquals('Second message', $result[1]['content'][0]['text']);
    }

    public function test_it_handles_multiple_images(): void
    {
        $input = ResponseInput::text('Compare these')
            ->addImage('https://example.com/image1.jpg')
            ->addImage('https://example.com/image2.jpg');

        $this->assertTrue($input->hasImages());

        $result = $input->toArray();
        $content = $result[0]['content'];
        $this->assertCount(3, $content); // 1 text + 2 images
        $this->assertEquals('input_text', $content[0]['type']);
        $this->assertEquals('input_image', $content[1]['type']);
        $this->assertEquals('input_image', $content[2]['type']);
    }

    public function test_simple_text_returns_array_structure(): void
    {
        $input = ResponseInput::text('Simple text');

        $result = $input->toArray();
        $this->assertIsArray($result);
        $this->assertCount(1, $result);
        $this->assertEquals('user', $result[0]['role']);
        $this->assertEquals('input_text', $result[0]['content'][0]['type']);
        $this->assertEquals('Simple text', $result[0]['content'][0]['text']);
    }

    public function test_to_array_always_returns_array(): void
    {
        $input = ResponseInput::text('Text')
            ->addImage('https://example.com/image.jpg');

        $result = $input->toArray();
        $this->assertIsArray($result);
    }

    public function test_get_messages_returns_all_messages(): void
    {
        $input = ResponseInput::text('First')
            ->newMessage()
            ->addText('Second');

        $messages = $input->getMessages();
        $this->assertCount(2, $messages);
    }
}
