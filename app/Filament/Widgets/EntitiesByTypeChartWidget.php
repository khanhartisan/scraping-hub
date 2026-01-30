<?php

namespace App\Filament\Widgets;

use App\Enums\EntityType;
use App\Models\Entity;
use Filament\Widgets\ChartWidget;

class EntitiesByTypeChartWidget extends ChartWidget
{
    protected static ?int $sort = 3;

    protected ?string $heading = 'Entities by type';

    protected ?string $description = 'Content type distribution';

    protected ?string $maxHeight = '300px';

    protected function getType(): string
    {
        return 'bar';
    }

    /**
     * @return array<string, mixed>
     */
    protected function getData(): array
    {
        $counts = Entity::query()
            ->selectRaw('type, count(*) as count')
            ->groupBy('type')
            ->pluck('count', 'type')
            ->all();

        $labels = [];
        $data = [];

        foreach (EntityType::cases() as $type) {
            $labels[] = $type->name;
            $data[] = $counts[$type->value] ?? 0;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Entities',
                    'data' => $data,
                    'backgroundColor' => 'rgba(245, 158, 11, 0.6)',
                    'borderColor' => 'rgb(245, 158, 11)',
                    'borderWidth' => 1,
                ],
            ],
            'labels' => $labels,
        ];
    }
}
