<?php

namespace App\Contracts\Scraper;

use App\Concerns\Serializable as SerializableTrait;
use App\Contracts\Serializable;

final class ScrapingOptions implements Serializable
{
    use SerializableTrait;
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

    /**
     * Convert the scraping options to an array representation.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'scraping_country_code' => $this->scrapingCountryCode ?? null,
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
        $options = new static();

        if (isset($data['scraping_country_code'])) {
            $options->setScrapingCountryCode($data['scraping_country_code']);
        }

        return $options;
    }
}