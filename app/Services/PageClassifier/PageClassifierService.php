<?php

namespace App\Services\PageClassifier;

use App\Contracts\PageClassifier\ClassificationResult;
use App\Contracts\PageClassifier\Classifier;

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
        // Remove script and style tags
        $html = preg_replace('/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/mi', '', $html);
        $html = preg_replace('/<style\b[^<]*(?:(?!<\/style>)<[^<]*)*<\/style>/mi', '', $html);

        // Decode HTML entities
        $html = html_entity_decode($html, ENT_QUOTES | ENT_HTML5, 'UTF-8');

        // Remove excessive whitespace
        $html = preg_replace('/\s+/', ' ', $html);
        $html = trim($html);

        // Limit HTML length to avoid token limits
        $maxLength = $this->config['max_html_length'] ?? 50000;
        if (strlen($html) > $maxLength) {
            $html = substr($html, 0, $maxLength) . '... [truncated]';
        }

        return $html;
    }

    /**
     * Perform the actual classification.
     * This method must be implemented by child classes.
     */
    abstract protected function performClassification(string $html): ClassificationResult;
}
