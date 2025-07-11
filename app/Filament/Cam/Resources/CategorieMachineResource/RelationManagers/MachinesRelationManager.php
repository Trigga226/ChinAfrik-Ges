<?php

namespace App\Filament\Cam\Resources\CategorieMachineResource\RelationManagers;

use App\Models\Machine;
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

class MachinesRelationManager extends RelationManager
{
    protected static string $relationship = 'machines';

    public function form(Form $form): Form
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
                        Forms\Components\TextInput::make('designation')->required()->unique(ignoreRecord: Machine::class),
                        Forms\Components\TextInput::make('immatriculation')->required()->unique(ignoreRecord: Machine::class),
                        Forms\Components\TextInput::make('marque')->required(),
                        DatePicker::make('date_mise_en_service'),
                        Forms\Components\Select::make('status')
                            ->options([
                                "disponible" => "disponible",
                                "indisponible" => "indisponible",
                            ])->searchable()
                            ->required(),
                        Forms\Components\Textarea::make('observation')->rows(3),
                    ])->columns(3)
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('designation')
            ->columns([
                Tables\Columns\TextColumn::make('categories.designation')->label('Catégorie')->sortable()->searchable()->weight(FontWeight::Bold)->toggleable(),
                Tables\Columns\TextColumn::make('designation')->label("Désignation")->sortable()->searchable()->badge()->color(Color::Emerald)->toggleable(),
                Tables\Columns\TextColumn::make('marque')->label("Marque")->sortable()->searchable()->color(Color::Blue)->toggleable(),
                Tables\Columns\TextColumn::make('immatriculation')->label("Immatriculation")->sortable()->searchable()->color(Color::Amber)->toggleable(),
                Tables\Columns\TextColumn::make('date_mise_en_service')->label("Date de mise en service")->sortable()->searchable()->badge()->color(Color::Purple)->toggleable(),
                Tables\Columns\TextColumn::make('status')->label("Statut")->sortable()->searchable()->toggleable()
                    ->badge()->color(fn (string $state): string => match ($state) {
                        'disponible' => 'success',
                        'indisponible' => 'danger',
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
