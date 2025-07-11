<?php

namespace App\Filament\Cam\Resources\DepenseCamionResource\Pages;

use App\Filament\Cam\Resources\DepenseCamionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDepenseCamions extends ListRecords
{
    protected static string $resource = DepenseCamionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
