<?php

namespace App\Filament\Cam\Resources\PointageCamionResource\Pages;

use App\Filament\Cam\Resources\PointageCamionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPointageCamions extends ListRecords
{
    protected static string $resource = PointageCamionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
