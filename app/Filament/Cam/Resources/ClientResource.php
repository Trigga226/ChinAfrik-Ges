<?php

namespace App\Filament\Cam\Resources;

use App\Filament\Cam\Resources\ClientResource\Pages;
use App\Filament\Cam\Resources\ClientResource\RelationManagers;
use App\Models\Client;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ClientResource extends Resource
{
    protected static ?string $model = Client::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
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

    public static function table(Table $table): Table
    {
        return $table
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
            'index' => Pages\ListClients::route('/'),
            'create' => Pages\CreateClient::route('/create'),
            'view' => Pages\ViewClient::route('/{record}'),
            'edit' => Pages\EditClient::route('/{record}/edit'),
        ];
    }
}
