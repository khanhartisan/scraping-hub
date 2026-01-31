<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \App\Contracts\PageClassifier\ClassificationResult classify(string $html)
 * @method static \App\Contracts\PageClassifier\Classifier driver(string|null $driver = null)
 *
 * @see \App\Services\PageClassifier\PageClassifierManager
 */
class PageClassifier extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return 'page_classifier.manager';
    }
}
