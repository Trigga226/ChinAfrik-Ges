<?php

namespace App\Filament\Cam\Resources\LocationCamionResource\RelationManagers;

use App\Models\Client;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ClientsRelationManager extends RelationManager
{
    protected static string $relationship = 'clients';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        TextInput::make('designation')->required()->unique(ignoreRecord: true),
                        TextInput::make('email')->label("E-mail")->email(),
                        TextInput::make('phone')->label("Téléphone")->tel(),
                        TextInput::make('domaine')->label("Domaine")
                            ->datalist(Client::all()->pluck('domaine','domaine')),
                        Forms\Components\Textarea::make('observation')->label("Observation")->rows(3),
                        Forms\Components\FileUpload::make('logo')->label("Logo")->image()
                            ->disk('public')->directory('clients')
                            ->downloadable(),
                    ])->columns(4)
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('designation')
            ->columns([
                ImageColumn::make('logo')->circular()->defaultImageUrl("/logo.png"),
                Tables\Columns\TextColumn::make('designation')->label("Designation")->searchable()->sortable()->weight(FontWeight::Bold),
                Tables\Columns\TextColumn::make('email')->label("E-mail")->searchable()->sortable()->badge()->color(Color::Emerald),
                Tables\Columns\TextColumn::make('phone')->label("Téléphone")->searchable()->sortable()->badge()->color(Color::Blue),
                Tables\Columns\TextColumn::make('domaine')->label("Domaine")->searchable()->sortable()->badge()->color(Color::Pink),
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
