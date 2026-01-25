<?php

namespace App\Contracts\PageParser;

use Carbon\Carbon;

final class PageData
{
    protected string $title;

    protected string $excerpt;

    protected string $thumbnailUrl;

    protected string $markdownContent;

    protected Carbon $publishedAt;

    protected Carbon $updatedAt;

    protected Carbon $fetchedAt;
}