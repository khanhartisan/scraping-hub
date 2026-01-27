<?php

namespace App\Providers;

use App\Contracts\Scraper\Scraper;
use App\Services\Scraper\ScraperManager;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(Scraper::class, ScraperManager::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
