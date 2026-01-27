<?php

namespace App\Services\PageParser;

use App\Contracts\PageParser\PageData;
use App\Contracts\PageParser\Parser;
use App\Utils\HtmlCleaner;

abstract class PageParserService implements Parser
{
    protected array $config;

    public function __construct(array $config = [])
    {
        $this->config = $config;
    }

    /**
     * Parse a page from HTML content.
     */
    public function parse(string $html): PageData
    {
        // Clean and prepare HTML
        $preparedHtml = $this->prepareHtml($html);

        // Perform parsing - this is implemented by child classes
        $pageData = $this->performParsing($preparedHtml);

        // Set fetchedAt timestamp
        $pageData->setFetchedAt(now());

        return $pageData;
    }

    /**
     * Prepare HTML for parsing.
     * Child classes can override this to customize HTML preparation.
     */
    protected function prepareHtml(string $html): string
    {
        $maxLength = $this->config['max_html_length'] ?? 100000;

        return HtmlCleaner::clean($html, $maxLength);
    }

    /**
     * Perform the actual parsing.
     * This method must be implemented by child classes.
     */
    abstract protected function performParsing(string $html): PageData;
}
