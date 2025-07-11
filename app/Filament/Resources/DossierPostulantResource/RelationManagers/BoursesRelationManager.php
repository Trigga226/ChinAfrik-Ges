<?php

namespace App\Filament\Resources\DossierPostulantResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BoursesRelationManager extends RelationManager
{
    protected static string $relationship = 'bourses';

    public function form(Form $form): Form
    {
        return $form
            ->schema([

                Wizard::make([
                    Wizard\Step::make('Générale')
                        ->description("Informations générales sur la bourse")
                        ->schema([
                            Forms\Components\TextInput::make('titre')->label("Titre")->required()->unique(ignoreRecord: true)->prefixIcon("heroicon-o-bars-2"),
                            Forms\Components\TextInput::make('cout')->label("Cout")->required()->prefixIcon("heroicon-o-currency-dollar")->numeric()->suffix("F CFA"),
                            Forms\Components\TextInput::make('frais')->label("Frais d'inscription")->required()->prefixIcon("heroicon-o-currency-dollar")->numeric()->suffix('F CFA'),
                        ]),
                    Wizard\Step::make('Description')
                        ->description("Description de la bourse")
                        ->schema([
                            Forms\Components\Textarea::make('description')->label("Description")->required(),
                        ]),
                    Wizard\Step::make('Prérequis')
                        ->description("Les prérequis de la bourse")
                        ->schema([
                            Forms\Components\Select::make('requis')->label("Prérequis")->options([
                                "Diplome du Bacalauréat"=>"Diplome du Bacalauréat",
                                "Diplome du Licence"=>"Diplome du Licence",
                                "Rélevé de note"=>"Rélevé de note",
                                "Age inferieur a 26"=>"Age inferieur a 26",
                                "Age inferieur a 25"=>"Age inferieur a 25",
                                "Passeport"=>"Passeport",
                            ])->searchable()->multiple(),
                        ]),

                ])->columnSpanFull()->skippable()
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('titre')
            ->columns([
                Tables\Columns\TextColumn::make('titre')->label("Titre")->searchable()->searchable()->weight(FontWeight::Bold),
                Tables\Columns\TextColumn::make('description')->label("Description")->searchable()->searchable()->limit(50)->weight(FontWeight::Medium),
                Tables\Columns\TextColumn::make('requis')->label("Prérequis")->searchable()->searchable()->limit(5)->weight(FontWeight::Medium)->badge(),
                Tables\Columns\TextColumn::make('frais')->label("Frais d'inscription")->searchable()->searchable()->weight(FontWeight::Medium)->badge()->color('info')->money('XOF', locale: 'fr'),
                Tables\Columns\TextColumn::make('cout')->label("Cout")->searchable()->searchable()->weight(FontWeight::Medium)->badge()->color('success')->money('XOF', locale: 'fr'),
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
