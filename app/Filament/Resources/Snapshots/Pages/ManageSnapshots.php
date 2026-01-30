<?php

namespace App\Filament\Resources\Snapshots\Pages;

use App\Filament\Resources\Snapshots\SnapshotResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageSnapshots extends ManageRecords
{
    protected static string $resource = SnapshotResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
