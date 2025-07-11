<?php

namespace App\Filament\Cam\Resources\DepenseCamionResource\Pages;

use App\Filament\Cam\Resources\DepenseCamionResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewDepenseCamion extends ViewRecord
{
    protected static string $resource = DepenseCamionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
