<?php

namespace App\Filament\Resources\DossierPostulantResource\Pages;

use App\Filament\Resources\DossierPostulantResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDossierPostulant extends EditRecord
{
    protected static string $resource = DossierPostulantResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make()->visible(auth()->user()->hasRole('super_admin')),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
