<?php

namespace App\Contracts\PageParser;

use App\Concerns\Serializable as SerializableTrait;
use App\Contracts\Serializable;
use Carbon\Carbon;

final class PageData implements Serializable
{
    use SerializableTrait;
    protected string $title = '';

    protected string $excerpt = '';

    protected string $thumbnailUrl = '';

    protected string $markdownContent = '';

    protected ?Carbon $publishedAt = null;

    protected ?Carbon $updatedAt = null;

    protected ?Carbon $fetchedAt = null;

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;
        return $this;
    }

    public function getExcerpt(): string
    {
        return $this->excerpt;
    }

    public function setExcerpt(string $excerpt): static
    {
        $this->excerpt = $excerpt;
        return $this;
    }

    public function getThumbnailUrl(): string
    {
        return $this->thumbnailUrl;
    }

    public function setThumbnailUrl(string $thumbnailUrl): static
    {
        $this->thumbnailUrl = $thumbnailUrl;
        return $this;
    }

    public function getMarkdownContent(): string
    {
        return $this->markdownContent;
    }

    public function setMarkdownContent(string $markdownContent): static
    {
        $this->markdownContent = $markdownContent;
        return $this;
    }

    public function getPublishedAt(): ?Carbon
    {
        return $this->publishedAt;
    }

    public function setPublishedAt(?Carbon $publishedAt): static
    {
        $this->publishedAt = $publishedAt;
        return $this;
    }

    public function getUpdatedAt(): ?Carbon
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?Carbon $updatedAt): static
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    public function getFetchedAt(): ?Carbon
    {
        return $this->fetchedAt;
    }

    public function setFetchedAt(?Carbon $fetchedAt): static
    {
        $this->fetchedAt = $fetchedAt;
        return $this;
    }

    /**
     * Convert the page data to an array representation.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'title' => $this->title,
            'excerpt' => $this->excerpt,
            'thumbnail_url' => $this->thumbnailUrl,
            'markdown_content' => $this->markdownContent,
            'published_at' => $this->publishedAt?->toIso8601String(),
            'updated_at' => $this->updatedAt?->toIso8601String(),
            'fetched_at' => $this->fetchedAt?->toIso8601String(),
        ];
    }

    /**
     * Create an instance from an array representation.
     *
     * @param  array<string, mixed>  $data
     * @return static
     */
    public static function fromArray(array $data): static
    {
        $pageData = new static();

        if (isset($data['title'])) {
            $pageData->setTitle($data['title']);
        }

        if (isset($data['excerpt'])) {
            $pageData->setExcerpt($data['excerpt']);
        }

        if (isset($data['thumbnail_url'])) {
            $pageData->setThumbnailUrl($data['thumbnail_url']);
        }

        if (isset($data['markdown_content'])) {
            $pageData->setMarkdownContent($data['markdown_content']);
        }

        if (isset($data['published_at'])) {
            $pageData->setPublishedAt($data['published_at'] ? Carbon::parse($data['published_at']) : null);
        }

        if (isset($data['updated_at'])) {
            $pageData->setUpdatedAt($data['updated_at'] ? Carbon::parse($data['updated_at']) : null);
        }

        if (isset($data['fetched_at'])) {
            $pageData->setFetchedAt($data['fetched_at'] ? Carbon::parse($data['fetched_at']) : null);
        }

        return $pageData;
    }
}