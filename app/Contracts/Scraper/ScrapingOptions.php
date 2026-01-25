<?php

namespace App\Contracts\Scraper;

final class ScrapingOptions
{
    protected string $scrapingCountryCode;

    public function getScrapingCountryCode(): ?string
    {
        return $this->scrapingCountryCode ?? null;
    }

    public function setScrapingCountryCode(string $scrapingCountryCode): static
    {
        $this->scrapingCountryCode = $scrapingCountryCode;
        return $this;
    }
}