<?php

namespace App\Filament\Cam\Resources;

use App\Filament\Cam\Resources\DepenseCamionResource\Pages;
use App\Filament\Cam\Resources\DepenseCamionResource\RelationManagers;
use App\Models\DepenseCamion;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DepenseCamionResource extends Resource
{
    protected static ?string $model = DepenseCamion::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                ->schema([
                    Select::make('camion')
                    ->relationship('camions','designation')
                    ->searchable()
                    ->preload()->required(),

                    DatePicker::make('date')->required()->label("Date dépense"),

                    TextInput::make("motif")->required(),

                    TextInput::make("montant")->numeric()->required()->suffix("FCFA"),

                    Textarea::make("description"),


                    FileUpload::make("piece")->label("Pièce comptable")->required()
                    ->disk('public')->directory('pieces')->downloadable()->multiple(),


                    ])->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('camion')->searchable()->sortable()->weight(FontWeight::Bold),
                TextColumn::make('date')->searchable()->sortable()->badge()->color('info'),
                TextColumn::make('motif')->searchable()->sortable()->limit(50),
                TextColumn::make('description')->searchable()->sortable()->limit(50),
                TextColumn::make('montant')->searchable()->sortable()->money(currency:'XOF')->badge()->color('warning'),
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
            'index' => Pages\ListDepenseCamions::route('/'),
            'create' => Pages\CreateDepenseCamion::route('/create'),
            'view' => Pages\ViewDepenseCamion::route('/{record}'),
            'edit' => Pages\EditDepenseCamion::route('/{record}/edit'),
        ];
    }
}
