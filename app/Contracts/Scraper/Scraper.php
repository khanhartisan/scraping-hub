<?php

namespace App\Contracts\Scraper;

use Psr\Http\Message\ResponseInterface;

interface Scraper
{
    public function fetch(string $url, ?ScrapingOptions $options = null): ResponseInterface;
}