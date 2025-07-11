<?php

namespace App\Filament\Cam\Resources;

use App\Filament\Cam\Resources\CamionResource\Pages;
use App\Filament\Cam\Resources\CamionResource\RelationManagers;
use App\Models\Camion;
use App\Models\Machine;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CamionResource extends Resource
{
    protected static ?string $model = Camion::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup="Gestion des camions";

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                ->schema([
                    Forms\Components\Select::make('categorie')
                    ->relationship('categories','designation')
                    ->required()
                    ->searchable()
                    ->preload(),
                    Forms\Components\TextInput::make('designation')->required()->unique(ignoreRecord: Camion::class),
                    Forms\Components\TextInput::make('immatriculation')->required()->unique(ignoreRecord: Camion::class),
                    Forms\Components\TextInput::make('marque')->required(),
                    DatePicker::make('date_mise_en_service'),
                    Forms\Components\Select::make('status')
                    ->options([
                        "disponible" => "disponible",
                        "indisponible" => "indisponible",
                    ])->searchable()
                    ->required(),
                    TextInput::make('cout')->numeric()->required()->suffix("FCFA"),
                    Forms\Components\Textarea::make('observation')->rows(3),
                ])->columns(3),


            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('categories.designation')->label('Catégorie')->sortable()->searchable()->weight(FontWeight::Bold)->toggleable(),
                Tables\Columns\TextColumn::make('designation')->label("Désignation")->sortable()->searchable()->badge()->color(Color::Emerald)->toggleable(),
                Tables\Columns\TextColumn::make('marque')->label("Marque")->sortable()->searchable()->color(Color::Blue)->toggleable(),
                Tables\Columns\TextColumn::make('immatriculation')->label("Immatriculation")->sortable()->searchable()->color(Color::Amber)->toggleable(),
                Tables\Columns\TextColumn::make('date_mise_en_service')->label("Date de mise en service")->sortable()->searchable()->badge()->color(Color::Red)->toggleable(),
                Tables\Columns\TextColumn::make('cout')->label("Cout de location (jour)")->sortable()->searchable()->badge()->color(Color::Purple)->toggleable()->money(currency: 'XOF',locale: 'fr_FR'),
                Tables\Columns\TextColumn::make('status')->label("Statut")->sortable()->searchable()->toggleable()
                ->badge()->color(fn (string $state): string => match ($state) {
                        'disponible' => 'success',
                        'indisponible' => 'danger',
                    }),
            ])
            ->filters([
                SelectFilter::make('status')
                ->options(Camion::all()->pluck('status','status'))
                ->searchable()
                    ->multiple()
                ->preload(),

                SelectFilter::make('categorie')
                ->relationship('categories','designation')
                ->searchable()
                    ->multiple()
                ->preload(),
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
            RelationManagers\LocationsRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCamions::route('/'),
            'create' => Pages\CreateCamion::route('/create'),
            'view' => Pages\ViewCamion::route('/{record}'),
            'edit' => Pages\EditCamion::route('/{record}/edit'),
        ];
    }
}
