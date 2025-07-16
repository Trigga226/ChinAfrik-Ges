<?php

namespace App\Filament\Cam\Resources\SuiviCamionResource\Pages;

use App\Filament\Cam\Resources\SuiviCamionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSuiviCamions extends ListRecords
{
    protected static string $resource = SuiviCamionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
