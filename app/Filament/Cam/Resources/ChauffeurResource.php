<?php

namespace App\Filament\Cam\Resources;

use App\Filament\Cam\Resources\ChauffeurResource\Pages;
use App\Filament\Cam\Resources\ChauffeurResource\RelationManagers;
use App\Models\Chauffeur;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ChauffeurResource extends Resource
{
    protected static ?string $model = Chauffeur::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $label="Chauffeurs";




    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                ->schema([
                    Forms\Components\TextInput::make('nom')->required(),
                    Forms\Components\TextInput::make('prenom')->required(),
                    Forms\Components\TextInput::make('cni')->label("Refenrece CNI")->required(),
                    Forms\Components\TextInput::make('phone')->label("Téléphone")->required(),
                    Forms\Components\FileUpload::make('scann_doc')->disk('public')->directory('scann')->downloadable()
                    ->panelLayout('grid'),
                ])->columns(3)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nom')->label('Nom')->searchable()->sortable()->weight(FontWeight::Bold),
                TextColumn::make('prenom')->label('Prénom')->searchable()->sortable()->weight(FontWeight::Bold),
                TextColumn::make('cni')->label('Refenrece CNI')->searchable()->sortable()->badge()->color('danger'),
                TextColumn::make('phone')->label('Téléphone')->searchable()->sortable()->badge()->color('info'),
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListChauffeurs::route('/'),
            'create' => Pages\CreateChauffeur::route('/create'),
            'view' => Pages\ViewChauffeur::route('/{record}'),
            'edit' => Pages\EditChauffeur::route('/{record}/edit'),
        ];
    }
}
