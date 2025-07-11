<?php

namespace App\Filament\Cam\Resources\CategorieCamionResource\Pages;

use App\Filament\Cam\Resources\CategorieCamionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCategorieCamions extends ListRecords
{
    protected static string $resource = CategorieCamionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
