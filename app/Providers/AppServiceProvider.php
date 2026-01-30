<?php

namespace App\Providers;

use App\Contracts\FileVision\FileVision;
use App\Contracts\OpenAI\OpenAIClient;
use App\Contracts\PageClassifier\Classifier;
use App\Contracts\PageParser\Parser;
use App\Contracts\ScrapePolicyEngine\ScrapePolicyEngine;
use App\Contracts\Scraper\Scraper;
use App\Services\FileVision\FileVisionManager;
use App\Services\OpenAI\OpenAIManager;
use App\Services\PageClassifier\PageClassifierManager;
use App\Services\PageParser\PageParserManager;
use App\Services\ScrapePolicyEngine\ScrapePolicyEngineManager;
use App\Services\Scraper\ScraperManager;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(OpenAIClient::class, OpenAIManager::class);
        $this->app->singleton(Scraper::class, ScraperManager::class);
        $this->app->singleton(FileVision::class, FileVisionManager::class);
        $this->app->singleton(Classifier::class, PageClassifierManager::class);
        $this->app->singleton(Parser::class, PageParserManager::class);
        $this->app->singleton(ScrapePolicyEngine::class, ScrapePolicyEngineManager::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
