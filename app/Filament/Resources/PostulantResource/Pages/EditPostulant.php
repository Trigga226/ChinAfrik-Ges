<?php

namespace App\Filament\Resources\PostulantResource\Pages;

use App\Filament\Resources\PostulantResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPostulant extends EditRecord
{
    protected static string $resource = PostulantResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }


    protected function afterActionCalled(): void
    {
        dd('tp');
    }
}
