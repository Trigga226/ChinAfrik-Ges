<?php

namespace App\Filament\Resources\BourseResource\Pages;

use App\Filament\Resources\BourseResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateBourse extends CreateRecord
{
    protected static string $resource = BourseResource::class;
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
