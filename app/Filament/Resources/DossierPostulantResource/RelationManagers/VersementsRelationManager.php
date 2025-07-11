<?php

namespace App\Filament\Resources\DossierPostulantResource\RelationManagers;

use App\Models\DossierPostulant;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class VersementsRelationManager extends RelationManager
{
    protected static string $relationship = 'versements';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\Select::make('dossier_id')
                            ->label('Dossier')
                            ->relationship('dossiers', 'nom_complet')
                            ->getOptionLabelFromRecordUsing(fn (DossierPostulant $record): string => "{$record->nom_complet} - {$record->phone}")
                            ->searchable()
                            ->required()
                            ->preload(),
                        DatePicker::make('date_versement')->required()->format('d/m/Y'),
                        Select::make('motif')
                            ->label('Motif')
                            ->options([
                                "Versement frais d'inscription" => "Versement frais d'inscription",
                                "Versement frais boursier" => "Versement frais boursier",
                            ])->searchable()->required(),
                        Select::make('moyen_versement')
                            ->label('Mode de paiement')
                            ->options([
                                "Cash" => "Cash",
                                "Virement" => "Virement",
                                "Cheque" => "Cheque",
                                "Mobile money" => "Mobile money",
                            ])->searchable()->required(),

                        TextInput::make('montant')->required()->numeric()->suffix('FCFA')->label('Montant'),



                    ])->columnSpanFull()->columns(2)
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                TextColumn::make('reference')->label('Reference')->weight(FontWeight::Bold),
                TextColumn::make('dossiers.nom_complet')->label('Dossier')
                    ->description(function (Model $record) {
                        return "Téléphone: ". $record->dossiers->phone;
                    })->weight(FontWeight::Bold)->searchable()->sortable(),
                TextColumn::make('motif')->label('Motif')->searchable()->sortable()->color('warning'),
                TextColumn::make('montant')->label('Montant')->searchable()->sortable()->money(currency: 'XOF', locale: 'fr')->badge()->color('success'),
                TextColumn::make('date_versement')->label('Date versement')->searchable()->sortable()->date(),
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
