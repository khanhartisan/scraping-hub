<?php

namespace App\Filament\Widgets;

use App\Models\Entity;
use App\Models\Snapshot;
use App\Models\Source;
use App\Models\Vertical;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ScrapingHubStatsWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected ?string $heading = 'Overview';

    protected int | string | array $columnSpan = 'full';

    protected function getStats(): array
    {
        $verticalsCount = Vertical::query()->count();
        $sourcesCount = Source::query()->count();
        $entitiesCount = Entity::query()->count();
        $snapshotsCount = Snapshot::query()->count();

        $entitiesPerDay = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->startOfDay();
            $entitiesPerDay[] = Entity::query()
                ->whereDate('created_at', $date)
                ->count();
        }

        return [
            Stat::make('Verticals', $verticalsCount)
                ->description('Content categories')
                ->descriptionIcon(Heroicon::OutlinedRectangleStack)
                ->color('primary'),
            Stat::make('Sources', $sourcesCount)
                ->description('Base URLs to scrape')
                ->descriptionIcon(Heroicon::OutlinedLink)
                ->color('success'),
            Stat::make('Entities', $entitiesCount)
                ->description('Pages & assets tracked (last 7 days)')
                ->descriptionIcon(Heroicon::OutlinedDocumentText)
                ->chart($entitiesPerDay)
                ->color('info'),
            Stat::make('Snapshots', $snapshotsCount)
                ->description('Content versions stored')
                ->descriptionIcon(Heroicon::OutlinedPhoto)
                ->color('warning'),
        ];
    }
}
