<?php

namespace App\Console\Commands;

use App\Enums\EntityType;
use App\Enums\ScrapingStatus;
use App\Facades\PageClassifier;
use App\Facades\PageParser;
use App\Models\Entity;
use App\Models\Snapshot;
use App\Utils\HtmlCleaner;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class TestPageEntity extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:render-page-entity';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     * @throws \Exception
     */
    public function handle()
    {
        $entity = Entity::query()
            ->where('type', EntityType::PAGE)
            ->findOrFail($this->ask('Entity ID'));

        $action = $this->choice('Service', [
             'PageClassifier', 'PageParser', 'HtmlCleaner'
        ]);

        switch ($action) {
            case 'PageClassifier':
                $this->pageClassifier($entity);
                break;

            case 'PageParser':
                $this->pageParser($entity);
                break;

            case 'HtmlCleaner':
                $this->htmlCleaner($entity, $this->getHtml($entity));
                break;

            default:
                $this->error('Unknown service');
                break;
        }
    }

    protected function getSnapshot(Entity $entity): Snapshot
    {
        /** @var Snapshot */
        return $entity
            ->snapshots()
            ->where('scraping_status', ScrapingStatus::SUCCESS)
            ->orderByDesc('version')
            ->firstOrFail();
    }

    protected function getHtml(Entity $entity): string
    {
        return Storage::get($this->getSnapshot($entity)->file_path);
    }

    protected function pageClassifier(Entity $entity): void
    {
        $sanitizedHtml = HtmlCleaner::sanitize($this->getHtml($entity));
        $classification = PageClassifier::classify($sanitizedHtml);

        $this->table(['Key', 'Value'], [
            ['Description', $classification->getDescription()],
            ['Page Type', $classification->getPageType()->name],
            ['Content Type', $classification->getContentType()->name],
            ['Temporal', $classification->getTemporal()->name],
            ['Tags', implode(', ', $classification->getTags())],
        ]);
    }

    protected function pageParser(Entity $entity): void
    {
        $localPath = $this->ask('Save markdown to', storage_path('app/private/'.$entity->id.'.md'));

        $sanitizedHtml = HtmlCleaner::sanitize($this->getHtml($entity));
        $pageData = PageParser::parse($sanitizedHtml);
        file_put_contents($localPath, $pageData->getMarkdownContent());

        $this->table(['Key', 'Value'], [
            ['Title', $pageData->getTitle()],
            ['Excerpt', $pageData->getExcerpt()],
            ['Thumbnail', $pageData->getThumbnailUrl()],
            ['Published at', $pageData->getPublishedAt()?->format('Y-m-d H:i:s')],
            ['Updated at', $pageData->getUpdatedAt()?->format('Y-m-d H:i:s')],
            ['Fetched at', $pageData->getFetchedAt()?->format('Y-m-d H:i:s')],
            ['Canonical URL', $pageData->getCanonicalUrl()],
            ['Canonical Number', $pageData->getCanonicalNumber()],
            ['Markdown', 'Saved to: '.$localPath]
        ]);
    }

    protected function htmlCleaner(Entity $entity, string $html): void
    {
        $function = $this->choice('Function', [
            'minify', 'sanitize'
        ]);

        $localPath = $this->ask('Save to', storage_path('app/private/'.$entity->id.'.html'));

        $contents = match ($function) {
            'minify' => HtmlCleaner::minify($html),
            'sanitize' => HtmlCleaner::sanitize($html),
            default => throw new \Exception('Unknown function')
        };

        file_put_contents($localPath, $contents);
        $this->info('Saved to: '.$localPath);
    }
}
