<?php

namespace App\Filament\Cam\Resources\CamionResource\Pages;

use App\Filament\Cam\Resources\CamionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCamions extends ListRecords
{
    protected static string $resource = CamionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
