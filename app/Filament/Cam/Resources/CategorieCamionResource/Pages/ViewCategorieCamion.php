<?php

namespace App\Filament\Cam\Resources\CategorieCamionResource\Pages;

use App\Filament\Cam\Resources\CategorieCamionResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewCategorieCamion extends ViewRecord
{
    protected static string $resource = CategorieCamionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
