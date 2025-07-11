<?php

namespace App\Filament\Cam\Resources\PointageCamionResource\Pages;

use App\Filament\Cam\Resources\PointageCamionResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewPointageCamion extends ViewRecord
{
    protected static string $resource = PointageCamionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
