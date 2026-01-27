<?php

namespace App\Services\Scraper\Drivers;

use App\Contracts\Scraper\Scraper;
use App\Contracts\Scraper\ScrapingOptions;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;

class GuzzleDriver implements Scraper
{
    protected Client $client;

    public function __construct()
    {
        $this->client = new Client([
            'timeout' => 30,
            'connect_timeout' => 10,
            'allow_redirects' => [
                'max' => 5,
                'strict' => false,
                'referer' => true,
                'protocols' => ['http', 'https'],
                'track_redirects' => false,
            ],
            'http_errors' => false,
            'headers' => [
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
                'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
                'Accept-Language' => 'en-US,en;q=0.9',
                'Accept-Encoding' => 'gzip, deflate, br',
                'Connection' => 'keep-alive',
                'Upgrade-Insecure-Requests' => '1',
            ],
        ]);
    }

    /**
     * @throws GuzzleException
     */
    public function fetch(string $url, ?ScrapingOptions $options = null): ResponseInterface
    {
        $requestOptions = $this->buildRequestOptions($options);
        return $this->client->get($url, $requestOptions);
    }

    protected function buildRequestOptions(?ScrapingOptions $options): array
    {
        $requestOptions = [];

        if ($options !== null) {
            // Handle country code for Accept-Language header
            if ($countryCode = $options->getScrapingCountryCode()) {
                $requestOptions['headers'] = [
                    'Accept-Language' => $this->getLanguageHeaderForCountry($countryCode),
                ];
            }
        }

        return $requestOptions;
    }

    protected function getLanguageHeaderForCountry(string $countryCode): string
    {
        // Map common country codes to language codes
        $countryToLanguage = [
            'US' => 'en-US,en;q=0.9',
            'GB' => 'en-GB,en;q=0.9',
            'FR' => 'fr-FR,fr;q=0.9,en;q=0.8',
            'DE' => 'de-DE,de;q=0.9,en;q=0.8',
            'ES' => 'es-ES,es;q=0.9,en;q=0.8',
            'IT' => 'it-IT,it;q=0.9,en;q=0.8',
            'JP' => 'ja-JP,ja;q=0.9,en;q=0.8',
            'CN' => 'zh-CN,zh;q=0.9,en;q=0.8',
            'KR' => 'ko-KR,ko;q=0.9,en;q=0.8',
            'BR' => 'pt-BR,pt;q=0.9,en;q=0.8',
            'MX' => 'es-MX,es;q=0.9,en;q=0.8',
            'CA' => 'en-CA,en;q=0.9,fr;q=0.8',
            'AU' => 'en-AU,en;q=0.9',
            'IN' => 'en-IN,en;q=0.9,hi;q=0.8',
        ];

        return $countryToLanguage[strtoupper($countryCode)] ?? 'en-US,en;q=0.9';
    }
}
