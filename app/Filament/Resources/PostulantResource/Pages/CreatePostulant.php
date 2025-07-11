<?php

namespace App\Filament\Resources\PostulantResource\Pages;

use App\Filament\Resources\PostulantResource;
use App\Models\DossierPostulant;
use App\Models\User;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Hash;
use function Symfony\Component\String\u;

class CreatePostulant extends CreateRecord
{
    protected static string $resource = PostulantResource::class;


protected function afterActionCalled(): void
{
    dd('tp');
}
}
