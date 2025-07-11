<?php

namespace App\Filament\Cam\Resources\PointageCamionResource\Pages;

use App\Filament\Cam\Resources\PointageCamionResource;
use App\Models\Camion;
use App\Models\LocationCamion;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use phpDocumentor\Reflection\Location;

class EditPointageCamion extends EditRecord
{
    protected static string $resource = PointageCamionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }


}
