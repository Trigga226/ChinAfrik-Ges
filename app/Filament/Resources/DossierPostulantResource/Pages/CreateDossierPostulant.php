<?php

namespace App\Filament\Resources\DossierPostulantResource\Pages;

use App\Filament\Resources\DossierPostulantResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateDossierPostulant extends CreateRecord
{
    protected static string $resource = DossierPostulantResource::class;
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
