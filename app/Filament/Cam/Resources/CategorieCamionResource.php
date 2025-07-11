<?php

namespace App\Filament\Cam\Resources;

use App\Filament\Cam\Resources\CategorieCamionResource\Pages;
use App\Filament\Cam\Resources\CategorieCamionResource\RelationManagers;
use App\Models\CategorieCamion;
use Filament\Forms;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CategorieCamionResource extends Resource
{
    protected static ?string $model = CategorieCamion::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup="Gestion des camions";

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                ->schema([
                    Forms\Components\TextInput::make('designation')->required()->unique(ignoreRecord: CategorieCamion::class),
                    Textarea::make('description')->rows(3),
                ])->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('designation')->searchable()->sortable()->weight(FontWeight::Bold),
                Tables\Columns\TextColumn::make('description')->searchable()->sortable()->limit(50)->default("Aucune description"),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\CamionsRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCategorieCamions::route('/'),
            'create' => Pages\CreateCategorieCamion::route('/create'),
            'view' => Pages\ViewCategorieCamion::route('/{record}'),
            'edit' => Pages\EditCategorieCamion::route('/{record}/edit'),
        ];
    }
}
