<?php

namespace App\Filament\Cam\Resources\CamionResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class LocationsRelationManager extends RelationManager
{
    protected static string $relationship = 'locations';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\Select::make('client')
                            ->relationship('clients', 'designation')->label('Client')
                            ->searchable()
                            ->required()
                            ->preload(),
                        Forms\Components\Select::make('camions')
                            ->relationship('camions', 'designation')->label('Camions')
                            ->searchable()
                            ->multiple()
                            ->preload()
                            ->required(),
                        Forms\Components\TextInput::make('duree')->numeric()->required()->suffix("Jours"),
                        Forms\Components\TextInput::make('cout_jounalier')->numeric()->required()->suffix("Jours")->label("Cout Jounalier")->suffix("FCFA"),
                        DatePicker::make('date_debut')->required(),
                        Forms\Components\TextInput::make('remise')->required()->suffix("FCFA")->label("Remise")->numeric()->default(0),
                        Forms\Components\Select::make('statut')
                            ->options([
                                "En attente" => "En attente",
                                "En cours" => "En cours",
                                "Terminer"=>"Terminer",
                            ])->searchable()->required(),
                    ])->columns(3)
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('camion_id')
            ->columns([
                Tables\Columns\TextColumn::make('client')->label('Client')->searchable()->sortable()->weight(FontWeight::Bold),
                Tables\Columns\TextColumn::make('duree')->label('Durée')->searchable()->sortable()->suffix("Jours")->badge(),
                Tables\Columns\TextColumn::make('cout_jounalier')->label('Cout journalier')->searchable()->sortable()->money(currency: 'XOF', locale: 'fr_FR')->badge()->color(Color::Emerald),
                Tables\Columns\TextColumn::make('date_debut')->label('Date début')->searchable()->sortable()->badge()->color(Color::Blue),
                Tables\Columns\TextColumn::make('remise')->label('Remise')->searchable()->sortable()->money(currency: 'XOF', locale: 'fr_FR')->badge()->color(Color::Indigo),
                Tables\Columns\TextColumn::make('total_a_percevoir')->label('Total')->searchable()->sortable()->money(currency: 'XOF', locale: 'fr_FR')->badge()->color(Color::Amber),
                Tables\Columns\TextColumn::make('statut')->label('Statut')->searchable()->sortable()->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'En attente' => 'warning',
                        'En cours' => 'success',
                        'Terminer' => 'info',
                    }),
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
