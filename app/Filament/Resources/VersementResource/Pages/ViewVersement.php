<?php

namespace App\Filament\Resources\VersementResource\Pages;

use App\Filament\Resources\VersementResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewVersement extends ViewRecord
{
    protected static string $resource = VersementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
