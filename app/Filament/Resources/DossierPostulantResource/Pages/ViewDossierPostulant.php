<?php

namespace App\Filament\Resources\DossierPostulantResource\Pages;

use App\Filament\Resources\DossierPostulantResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewDossierPostulant extends ViewRecord
{
    protected static string $resource = DossierPostulantResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
