<?php

namespace App\Filament\Cam\Resources\DepenseMachineResource\Pages;

use App\Filament\Cam\Resources\DepenseMachineResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDepenseMachines extends ListRecords
{
    protected static string $resource = DepenseMachineResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
