<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VersementResource\Pages;
use App\Filament\Resources\VersementResource\RelationManagers;
use App\Models\DossierPostulant;
use App\Models\Postulant;
use App\Models\RecuPaiement;
use App\Models\Versement;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class VersementResource extends Resource
{
    protected static ?string $model = Versement::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $label="Versement";
    protected static ?string $pluralLabel="Versements";
    protected static ?string $navigationGroup="Comptabilité";


    protected static ?string $recordTitleAttribute = 'dossiers.nom_complet';

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'success';
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['dossier_id', 'motif', 'montant','date_versement','dossiers.nom_complet'];
    }

    public static function getGlobalSearchResultDetails(\Illuminate\Database\Eloquent\Model $record): array
    {

        return [
            'Postulant' => $record->dossiers->nom_complet,
            'Montant versé' => $record->montant.' FCFA',
            'Date versement'=>$record->date_versement,
            'Motif'=>$record->motif,
        ];
    }

    public static function form(Form $form): Form
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

    public static function table(Table $table): Table
    {
        return $table
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
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make("print")->icon('heroicon-o-printer')->color('info')->label("")

                    ->action(function (Versement $record)  {
                        return redirect()->route('recu',$record->id);
                    })
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
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
            'index' => Pages\ListVersements::route('/'),
            'create' => Pages\CreateVersement::route('/create'),
            'view' => Pages\ViewVersement::route('/{record}'),
            'edit' => Pages\EditVersement::route('/{record}/edit'),
        ];
    }


    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
}
