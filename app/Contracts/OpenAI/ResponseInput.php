<?php

namespace App\Contracts\OpenAI;

final class ResponseInput
{
    /**
     * @var array<int, array<string, mixed>>
     */
    protected array $messages = [];

    /**
     * @var array<int, array<string, mixed>>
     */
    protected array $currentContent = [];

    /**
     * Create a text-only input.
     */
    public static function text(string $text): self
    {
        return (new self)->addText($text);
    }

    /**
     * Create an input with text and image.
     */
    public static function withImage(string $text, string $imageUrl): self
    {
        return (new self)
            ->addText($text)
            ->addImage($imageUrl);
    }

    /**
     * Private constructor - use static factory methods.
     */
    private function __construct()
    {
    }

    /**
     * Add text content to the current message.
     */
    public function addText(string $text): self
    {
        $this->currentContent[] = [
            'type' => 'input_text',
            'text' => $text,
        ];

        return $this;
    }

    /**
     * Add an image URL to the current message.
     */
    public function addImage(string $imageUrl, ?string $detail = null): self
    {
        $imageItem = [
            'type' => 'input_image',
            'image_url' => $imageUrl,
        ];

        if ($detail !== null) {
            $imageItem['detail'] = $detail;
        }

        $this->currentContent[] = $imageItem;

        return $this;
    }

    /**
     * Add an image from base64 data.
     */
    public function addImageFromBase64(string $base64Data, string $mimeType = 'image/jpeg', ?string $detail = null): self
    {
        $dataUrl = "data:{$mimeType};base64,{$base64Data}";

        return $this->addImage($dataUrl, $detail);
    }

    /**
     * Add an image using a file ID (from Files API).
     */
    public function addImageFromFileId(string $fileId, ?string $detail = null): self
    {
        $imageItem = [
            'type' => 'input_image',
            'file_id' => $fileId,
        ];

        if ($detail !== null) {
            $imageItem['detail'] = $detail;
        }

        $this->currentContent[] = $imageItem;

        return $this;
    }

    /**
     * Start a new message (for multi-turn conversations).
     */
    public function newMessage(string $role = 'user'): self
    {
        // Save current message if it has content
        if (! empty($this->currentContent)) {
            $this->messages[] = [
                'role' => $role,
                'content' => $this->currentContent,
            ];
        }

        $this->currentContent = [];

        return $this;
    }

    /**
     * Convert to the format expected by the OpenAI Responses API.
     * Always returns an array structure.
     *
     * @return array<int, array<string, mixed>>
     */
    public function toArray(): array
    {
        // Build the final message array
        $messages = $this->messages;

        // Add current content if it exists
        if (! empty($this->currentContent)) {
            $messages[] = [
                'role' => 'user',
                'content' => $this->currentContent,
            ];
        }

        // Always return as array of messages
        return $messages;
    }

    /**
     * Get all messages.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getMessages(): array
    {
        $messages = $this->messages;

        if (! empty($this->currentContent)) {
            $messages[] = [
                'role' => 'user',
                'content' => $this->currentContent,
            ];
        }

        return $messages;
    }

    /**
     * Check if input contains images.
     */
    public function hasImages(): bool
    {
        $contentArrays = array_map(fn (array $msg) => $msg['content'] ?? [], $this->messages);
        $contentArrays[] = $this->currentContent;

        $allContent = array_merge(...$contentArrays);

        return ! empty(array_filter(
            $allContent,
            fn (array $item): bool => ($item['type'] ?? null) === 'input_image'
        ));
    }

    /**
     * Check if input is text-only.
     */
    public function isTextOnly(): bool
    {
        return ! $this->hasImages();
    }
}
