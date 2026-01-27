<?php

namespace App\Services\PageClassifier;

use App\Contracts\PageClassifier\ClassificationResult;
use App\Contracts\PageClassifier\Classifier;
use App\Utils\HtmlCleaner;

abstract class PageClassifierService implements Classifier
{
    protected array $config;

    public function __construct(array $config = [])
    {
        $this->config = $config;
    }

    /**
     * Classify a page from HTML content.
     */
    public function classify(string $html): ClassificationResult
    {
        // Clean and prepare HTML
        $preparedHtml = $this->prepareHtml($html);

        // Perform classification - this is implemented by child classes
        return $this->performClassification($preparedHtml);
    }

    /**
     * Prepare HTML for classification.
     * Child classes can override this to customize HTML preparation.
     */
    protected function prepareHtml(string $html): string
    {
        $maxLength = $this->config['max_html_length'] ?? 50000;

        return HtmlCleaner::clean($html, $maxLength);
    }

    /**
     * Perform the actual classification.
     * This method must be implemented by child classes.
     */
    abstract protected function performClassification(string $html): ClassificationResult;
}
