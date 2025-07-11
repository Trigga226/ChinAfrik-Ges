<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BourseResource\Pages;
use App\Filament\Resources\BourseResource\RelationManagers;
use App\Filament\Resources\BourseResource\Widgets\BourseCatChart;
use App\Filament\Resources\BourseResource\Widgets\BourseStat;
use App\Models\Bourse;
use Filament\Forms;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Table;
use Guava\FilamentModalRelationManagers\Actions\Table\RelationManagerAction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BourseResource extends Resource
{
    protected static ?string $model = Bourse::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';
    protected static ?string $navigationGroup="Gestion des Bourses";
    protected static ?string $recordTitleAttribute = 'titre';

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'success';
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['titre', 'description', 'requis',];
    }

    public static function getGlobalSearchResultDetails(\Illuminate\Database\Eloquent\Model $record): array
    {
        return [
            'Titre' => $record->titre,
            'Description' => $record->description,
        ];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Wizard::make([
                    Wizard\Step::make('Générale')
                        ->description("Informations générales sur la bourse")
                        ->schema([
                            Forms\Components\TextInput::make('titre')->label("Titre")->required()->unique(ignoreRecord: true)->prefixIcon("heroicon-o-bars-2"),
                            Forms\Components\TextInput::make('coutt')->label("Cout bourse totale")->required()->prefixIcon("heroicon-o-currency-dollar")->numeric()->suffix("F CFA"),
                            Forms\Components\TextInput::make('coutp')->label("Cout bourse partielle")->required()->prefixIcon("heroicon-o-currency-dollar")->numeric()->suffix("F CFA"),
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
                                "Age inferieur a 100"=>"Age inferieur a 100",
                                "Passeport"=>"Passeport",
                            ])->searchable()->multiple(),
                        ]),

                ])->columnSpanFull()->skippable()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('titre')->label("Titre")->searchable()->searchable()->weight(FontWeight::Bold),
                Tables\Columns\TextColumn::make('description')->label("Description")->searchable()->searchable()->limit(50)->weight(FontWeight::Medium),
                Tables\Columns\TextColumn::make('requis')->label("Prérequis")->searchable()->searchable()->limit(5)->weight(FontWeight::Medium)->badge(),
                Tables\Columns\TextColumn::make('frais')->label("Frais d'inscription")->searchable()->searchable()->weight(FontWeight::Medium)->badge()->color('info')->money('XOF', locale: 'fr'),
                Tables\Columns\TextColumn::make('coutt')->label("Cout bourse totale")->searchable()->searchable()->weight(FontWeight::Medium)->badge()->color('success')->money('XOF', locale: 'fr'),
                Tables\Columns\TextColumn::make('coutp')->label("Cout bourse partielle")->searchable()->searchable()->weight(FontWeight::Medium)->badge()->color('success')->money('XOF', locale: 'fr'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                ->modal(),
                Tables\Actions\EditAction::make()
                ->modal(),
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
            RelationManagers\DossiersRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBourses::route('/'),
            'create' => Pages\CreateBourse::route('/create'),
            'view' => Pages\ViewBourse::route('/{record}'),
            'edit' => Pages\EditBourse::route('/{record}/edit'),
        ];
    }

    public static function getWidgets(): array
    {
        return [BourseStat::class,];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
}
