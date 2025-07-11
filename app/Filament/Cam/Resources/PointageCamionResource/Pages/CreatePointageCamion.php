<?php

namespace App\Filament\Cam\Resources\PointageCamionResource\Pages;

use App\Filament\Cam\Resources\PointageCamionResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreatePointageCamion extends CreateRecord
{
    protected static string $resource = PointageCamionResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
       // dd($data);

       return $data;
    }


}
