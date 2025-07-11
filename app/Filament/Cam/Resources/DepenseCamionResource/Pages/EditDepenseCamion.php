<?php

namespace App\Filament\Cam\Resources\DepenseCamionResource\Pages;

use App\Filament\Cam\Resources\DepenseCamionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDepenseCamion extends EditRecord
{
    protected static string $resource = DepenseCamionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
