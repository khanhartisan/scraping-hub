<?php

namespace App\Providers;

use App\Contracts\FileVision\FileVision;
use App\Contracts\OpenAI\OpenAIClient;
use App\Contracts\PageClassifier\Classifier;
use App\Contracts\PageParser\Parser;
use App\Contracts\ScrapePolicyEngine\ScrapePolicyEngine;
use App\Contracts\Scraper\Scraper;
use App\Models\Client;
use App\Models\Entity;
use App\Models\EntityCount;
use App\Models\EntityRelation;
use App\Models\EntityVertical;
use App\Models\Model;
use App\Models\Snapshot;
use App\Models\Source;
use App\Models\SourceVertical;
use App\Models\User;
use App\Models\Vertical;
use App\Services\FileVision\FileVisionManager;
use App\Services\OpenAI\OpenAIManager;
use App\Services\PageClassifier\PageClassifierManager;
use App\Services\PageParser\PageParserManager;
use App\Services\ScrapePolicyEngine\ScrapePolicyEngineManager;
use App\Services\Scraper\ScraperManager;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

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
        Model::unguard();

        $this->registerMorphMap();
    }

    /**
     * Register the morph map with snake_case type names for all models.
     */
    protected function registerMorphMap(): void
    {
        $models = [
            Client::class,
            Entity::class,
            EntityCount::class,
            EntityRelation::class,
            EntityVertical::class,
            Snapshot::class,
            Source::class,
            SourceVertical::class,
            User::class,
            Vertical::class,
        ];

        $map = collect($models)->mapWithKeys(function (string $class) {
            return [Str::snake(class_basename($class)) => $class];
        })->all();

        Relation::morphMap($map);
    }
}
