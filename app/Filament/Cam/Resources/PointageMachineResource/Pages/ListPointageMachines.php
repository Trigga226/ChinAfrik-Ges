<?php

namespace App\Filament\Cam\Resources\PointageMachineResource\Pages;

use App\Filament\Cam\Resources\PointageMachineResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPointageMachines extends ListRecords
{
    protected static string $resource = PointageMachineResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
