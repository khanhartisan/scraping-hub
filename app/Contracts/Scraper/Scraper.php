<?php

namespace App\Contracts\Scraper;

use GuzzleHttp\Psr7\Response;

interface Scraper
{
    public function fetch(string $url, ?ScrapingOptions $options = null): Response;
}