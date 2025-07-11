<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MyEventResource\Pages;
use App\Filament\Resources\MyEventResource\RelationManagers;
use App\Models\Event;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MyEventResource extends Resource
{
    protected static ?string $model = Event::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar';
    protected static ?string $label="Evenement";
  protected static ?string $pluralLabel="Evenements";
    protected static ?string $navigationGroup="Administration";

    protected static ?string $recordTitleAttribute = 'nom_complet';

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'details',];
    }

    public static function getGlobalSearchResultDetails(\Illuminate\Database\Eloquent\Model $record): array
    {
        return [
            'Titre' => $record->name,
            'Détails' => $record->details,
        ];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                ->schema([
                    TextInput::make('name')->label('Titre')->required(),
                    Forms\Components\DateTimePicker::make('starts_at')->label('Date de debut')->required(),
                    Forms\Components\DateTimePicker::make('ends_at')->label('Date de fin')->required(),
                    Forms\Components\Textarea::make('details')->label('Description')->required(),
                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label("Titre")->sortable()->searchable(),
                Tables\Columns\TextColumn::make('starts_at')->label('Date de debut'),
                Tables\Columns\TextColumn::make('ends_at')->label('Date de fin'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                ->slideOver(),
                Tables\Actions\EditAction::make()
                ->slideOver(),
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
            'index' => Pages\ListMyEvents::route('/'),
          //  'create' => Pages\CreateMyEvent::route('/create'),
          //  'view' => Pages\ViewMyEvent::route('/{record}'),
          //  'edit' => Pages\EditMyEvent::route('/{record}/edit'),
        ];
    }
}
