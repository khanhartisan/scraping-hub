<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Scraper Driver
    |--------------------------------------------------------------------------
    |
    | This option controls the default scraper driver that will be used by
    | the application. The driver specified here will be used when no
    | explicit driver is specified when making scraping requests.
    |
    */

    'default' => env('SCRAPER_DRIVER', 'guzzle'),

    /*
    |--------------------------------------------------------------------------
    | Scraper Drivers
    |--------------------------------------------------------------------------
    |
    | Here you may configure the connection options for every scraper driver
    | used by your application. An example configuration is provided for
    | each driver supported. You're also free to add more drivers.
    |
    | Supported drivers: "guzzle"
    |
    */

    'drivers' => [

        'guzzle' => [
            'timeout' => env('SCRAPER_TIMEOUT', 30),
            'connect_timeout' => env('SCRAPER_CONNECT_TIMEOUT', 10),
            'verify' => env('SCRAPER_VERIFY_SSL', true),
            'allow_redirects' => [
                'max' => env('SCRAPER_MAX_REDIRECTS', 5),
                'strict' => false,
                'referer' => true,
                'protocols' => ['http', 'https'],
                'track_redirects' => false,
            ],
            'headers' => [
                'User-Agent' => env('SCRAPER_USER_AGENT', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36'),
                'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
                'Accept-Language' => 'en-US,en;q=0.9',
                'Accept-Encoding' => 'gzip, deflate, br',
                'Connection' => 'keep-alive',
                'Upgrade-Insecure-Requests' => '1',
            ],
        ],

    ],

];
