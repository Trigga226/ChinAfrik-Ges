<?php

namespace App\Filament\Cam\Resources\SuiviCamionResource\Pages;

use App\Filament\Cam\Resources\SuiviCamionResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewSuiviCamion extends ViewRecord
{
    protected static string $resource = SuiviCamionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
