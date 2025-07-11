<?php

namespace App\Filament\Cam\Resources;

use App\Filament\Cam\Resources\CategorieMachineResource\Pages;
use App\Filament\Cam\Resources\CategorieMachineResource\RelationManagers;
use App\Models\CategorieCamion;
use App\Models\CategorieMachine;
use Filament\Forms;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CategorieMachineResource extends Resource
{
    protected static ?string $model = CategorieMachine::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup="Gestion des machines";

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\TextInput::make('designation')->required()->unique(ignoreRecord: CategorieMachineResource::class),
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
            RelationManagers\MachinesRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCategorieMachines::route('/'),
            'create' => Pages\CreateCategorieMachine::route('/create'),
            'view' => Pages\ViewCategorieMachine::route('/{record}'),
            'edit' => Pages\EditCategorieMachine::route('/{record}/edit'),
        ];
    }
}
