<?php

namespace App\Filament\Resources\DossierPostulantResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;

class PostulantsRelationManager extends RelationManager
{
    protected static string $relationship = 'postulants';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        FileUpload::make('photo')->avatar()
                            ->disk('public')
                            ->directory('photos_postulants')
                            ->downloadable()
                            ->image()
                    ]),
                Forms\Components\Section::make()
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('nom_complet')->label('Nom complet')->required()->placeholder('Nom complet')->prefixIcon("heroicon-o-user"),
                        Forms\Components\TextInput::make('email')->label('E-mail')->required()->placeholder('E-mail')->prefixIcon("heroicon-o-envelope")->email()->unique(ignoreRecord: true),
                        PhoneInput::make('phone')->label('Téléphone')->required()->placeholder('Téléphone')->prefixIcon("heroicon-o-phone")->unique(ignoreRecord: true),
                        Select::make('genre')->options([
                            "Homme" => "Homme",
                            "Femme" => "Femme",
                        ])->searchable()->label('Genre')->required(),
                    ])
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('nom_complet')
            ->columns([
                ImageColumn::make('photo')->circular()->defaultImageUrl('/logo.png'),
                Tables\Columns\TextColumn::make('nom_complet')->label('Nom complet')->searchable()->sortable()->weight(FontWeight::Bold),
                Tables\Columns\TextColumn::make('email')->label('E-mail')->searchable()->sortable()->badge()->color('info'),
                Tables\Columns\TextColumn::make('phone')->label('Téléphone')->searchable()->sortable()->badge()->color('success'),
                Tables\Columns\TextColumn::make('genre')->label('Genre')->searchable()->sortable()->badge()->color('warning'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
