<?php

namespace App\Filament\Cam\Resources;

use App\Filament\Cam\Resources\SuiviCamionResource\Pages;
use App\Filament\Cam\Resources\SuiviCamionResource\RelationManagers;
use App\Models\Chauffeur;
use App\Models\SuiviCamion;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SuiviCamionResource extends Resource
{
    protected static ?string $model = SuiviCamion::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Suivis';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                ->schema([
                    Forms\Components\DatePicker::make('date')->required(),
                    Forms\Components\Select::make('camion')->label("Selectionnez un immatriculation de camion")
                    ->relationship('camions','immatriculation')
                    ->required()->searchable()->preload(),
                    Select::make('chauffeur')
                        ->relationship('chauffeurs','id')
                        ->getOptionLabelFromRecordUsing(fn (Chauffeur $record): string => " {$record->nom}  {$record->prenom}  ({$record->phone}) ")
                        ->searchable()->preload()->required(),
                    Select::make('type_entretient')->required()->searchable()->preload()
                    ->options([
                        "Entretient" => "Entretient",
                        "Réparation"=>"Réparation",
                    ]),
                    Forms\Components\TextInput::make('piece_change')->label('Piece Changé'),
                    Forms\Components\TextInput::make('kilometrage')->label('Kilometrage')->numeric()->required(),
                    Forms\Components\TextInput::make('duree_immobilisation')->label('Durée immobilisation')->required(),
                    Forms\Components\TextInput::make('atelier')->label('Atelier / Mecanicien')->required(),
                    Forms\Components\Textarea::make('decription_panne')->label('Description de la panne / entretient'),
                    Forms\Components\Textarea::make('observation')->label('Observation'),
                    FileUpload::make('document')->disk('public')->directory('doc')->columnSpanFull()->downloadable(),
                ])->columns(3)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('date')->searchable()->sortable()->weight(FontWeight::Bold),
                Tables\Columns\TextColumn::make('camions.immatriculation')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('chauffeurs.nom')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('type_entretient')->searchable()->sortable()->label('Type entretient')->badge()->color('danger'),
                Tables\Columns\TextColumn::make('atelier')->searchable()->sortable()->label('Atelier / Mecanicien')->badge()->color('info'),
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
            'index' => Pages\ListSuiviCamions::route('/'),
            'create' => Pages\CreateSuiviCamion::route('/create'),
            'view' => Pages\ViewSuiviCamion::route('/{record}'),
            'edit' => Pages\EditSuiviCamion::route('/{record}/edit'),
        ];
    }
}
