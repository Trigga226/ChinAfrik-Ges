<?php

namespace App\Filament\Cam\Resources\CategorieMachineResource\Pages;

use App\Filament\Cam\Resources\CategorieMachineResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCategorieMachines extends ListRecords
{
    protected static string $resource = CategorieMachineResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
