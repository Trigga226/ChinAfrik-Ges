<?php

namespace App\Filament\Cam\Resources\LocationCamionResource\Pages;

use App\Filament\Cam\Resources\LocationCamionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLocationCamions extends ListRecords
{
    protected static string $resource = LocationCamionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
