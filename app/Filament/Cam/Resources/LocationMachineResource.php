<?php

namespace App\Filament\Cam\Resources;

use App\Filament\Cam\Resources\LocationMachineResource\Pages;
use App\Filament\Cam\Resources\LocationMachineResource\RelationManagers;
use App\Models\Camion;
use App\Models\LocationMachine;
use App\Models\Machine;
use Filament\Forms;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Colors\Color;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Support\Enums\FontWeight;
use function Livewire\on;

class LocationMachineResource extends Resource
{
    protected static ?string $model = LocationMachine::class;

    protected static ?string $navigationGroup="Gestion des machines";
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\Select::make('client')
                            ->relationship('clients', 'designation')->label('Client')
                            ->searchable()
                            ->required()
                            ->preload(),

                        DatePicker::make('date')->label('Date')->required(),

                        Forms\Components\TextInput::make('remise')
                            ->required()
                            ->suffix("FCFA")
                            ->label("Remise")
                            ->numeric()
                            ->live(onBlur: true)
                            ->default(0),

                        Forms\Components\Select::make('statut')
                            ->options([
                                "En attente" => "En attente",
                                "En cours" => "En cours",
                                "Terminer"=>"Terminer",
                            ])
                            ->searchable()
                            ->required(),
                    ])->columns(3),

                Forms\Components\Section::make()
                    ->schema([
                        Repeater::make('details')
                            ->schema([
                                Select::make('machine')
                                    ->relationship('machines', 'designation')
                                    ->searchable()
                                    ->required()
                                    ->preload()
                                    ->live(),

                                TextInput::make('duree')
                                    ->label('Durée')
                                    ->numeric()
                                    ->required()
                                    ->suffix("Heures")
                                    ->live(onBlur: true)
                                    ->default(0)
                                    ->afterStateUpdated(function (TextInput $component, Forms\Get $get, Forms\Set $set) {
                                        if ($get('machine')){
                                            $machine = Machine::find($get('machine'));
                                            if ($machine) {
                                                $cout = $machine->cout ?? 0;
                                                $duree = $get('duree') ?? 0;
                                                $total = $cout * $duree;
                                                $set('montant', $total);
                                            }
                                        }
                                    }),

                                Hidden::make('montant')
                                    ->default(0),

                                Forms\Components\Placeholder::make('montant_affichage')
                                    ->label('Montant')
                                    ->content(function (Forms\Get $get) {
                                        if ($get('machine')){
                                            $machine = Machine::find($get('machine'));
                                            if ($machine) {
                                                $cout = $machine->cout ?? 0;
                                                $duree = $get('duree') ?? 0;
                                                $total = $cout * $duree;
                                                return number_format($total, 0, ',', ' ') . ' XOF';
                                            }
                                        }
                                        return '0 XOF';
                                    })
                            ])->live()
                            ->columns(3)
                            ->minItems(1)
                            ->required(),

                        Forms\Components\Placeholder::make('total')
                            ->label('Total à percevoir')
                            ->content(function (Forms\Get $get) {
                                $details = $get('details') ?? [];
                                $remise = $get('remise') ?? 0;
                                $total = 0;

                                foreach ($details as $detail) {
                                    if (isset($detail['montant'])) {
                                        $total += $detail['montant'];
                                    }
                                }

                                $total = $total - $remise;
                                return number_format($total, 0, ',', ' ') . ' XOF';
                            }),

                        Hidden::make('total_calculated')
                            ->default(0)
                            ->afterStateUpdated(function (Hidden $component, Forms\Get $get, Forms\Set $set) {
                                $details = $get('details') ?? [];
                                $remise = $get('remise') ?? 0;
                                $total = 0;

                                foreach ($details as $detail) {
                                    if (isset($detail['montant'])) {
                                        $total += $detail['montant'];
                                    }
                                }

                                $total = $total - $remise;
                                $set('total_calculated', $total);
                            })
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('client')
                    ->label('Client')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Bold),

                Tables\Columns\TextColumn::make('remise')
                    ->label('Remise')
                    ->searchable()
                    ->sortable()
                    ->money(currency: 'XOF', locale: 'fr_FR')
                    ->badge()
                    ->color(Color::Indigo),
                Tables\Columns\TextColumn::make('total_a_percevoir')
                    ->label('Total')
                    ->searchable()
                    ->sortable()
                    ->money(currency: 'XOF', locale: 'fr_FR')
                    ->badge()
                    ->color(Color::Amber),
                Tables\Columns\TextColumn::make('statut')
                    ->label('Statut')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'En attente' => 'warning',
                        'En cours' => 'success',
                        'Terminer' => 'info',
                    }),
            ])->defaultSort('created_at', 'DESC')
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
            'index' => Pages\ListLocationMachines::route('/'),
            'create' => Pages\CreateLocationMachine::route('/create'),
            'view' => Pages\ViewLocationMachine::route('/{record}'),
            'edit' => Pages\EditLocationMachine::route('/{record}/edit'),
        ];
    }
}
