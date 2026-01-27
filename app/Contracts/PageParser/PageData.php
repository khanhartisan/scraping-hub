<?php

namespace App\Contracts\PageParser;

use Carbon\Carbon;

final class PageData
{
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
}