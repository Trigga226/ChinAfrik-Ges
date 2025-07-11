<?php

namespace App\Filament\Resources\PostulantResource\Pages;

use App\Filament\Resources\PostulantResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewPostulant extends ViewRecord
{
    protected static string $resource = PostulantResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
