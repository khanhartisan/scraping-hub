<?php

namespace App\Console\Commands;

use App\Enums\EntityType;
use App\Enums\ScrapingStatus;
use App\Facades\PageParser;
use App\Models\Entity;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class RenderPageEntity extends Command
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
     */
    public function handle()
    {
        $entity = Entity::query()
            ->where('type', EntityType::PAGE)
            ->findOrFail($this->ask('Entity ID'));

        $snapshot = $entity->snapshots()->where('scraping_status', ScrapingStatus::SUCCESS)->orderByDesc('version')->firstOrFail();

        $html = Storage::get($snapshot->file_path);
        $pageData = PageParser::parse($html);

        $this->line($pageData->getMarkdownContent());
    }
}
