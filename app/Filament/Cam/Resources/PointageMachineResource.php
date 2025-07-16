<?php

namespace App\Filament\Cam\Resources;

use App\Filament\Cam\Resources\PointageMachineResource\Pages;
use App\Filament\Cam\Resources\PointageMachineResource\RelationManagers;
use App\Models\Camion;
use App\Models\Chauffeur;
use App\Models\LocationCamion;
use App\Models\LocationMachine;
use App\Models\Machine;
use App\Models\PointageCamion;
use App\Models\PointageMachine;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use DateTime;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Malzariey\FilamentDaterangepickerFilter\Filters\DateRangeFilter;

class PointageMachineResource extends Resource
{
    protected static ?string $model = PointageMachine::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup="Gestion des machines";

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->schema([
                        Select::make('location')
                            ->relationship('locations','id')
                            ->getOptionLabelFromRecordUsing(fn (LocationMachine $record): string => "Location de {$record->client} du {$record->date_debut} datant du {$record->created_at} ")
                            ->searchable()
                            ->required()
                            ->preload()->live(),
                        Select::make('machine')
                            ->options(function (Get $get): Collection {
                                $locationId = $get('location');


                                if (!$locationId) {
                                    return Collection::empty();
                                }

                                // For a many-to-many relationship, we need to query through the relationship
                                return Machine::query()
                                    ->whereHas('locations', function ($query) use ($locationId) {
                                        $query->where('location_id', $locationId);
                                    })
                                    ->pluck('designation', 'designation');
                            })->required()
                            ->preload()
                            ->searchable()
                            ->disabled(fn (Get $get): bool => !$get('location')),
                        Toggle::make('a_travailler')->label("A travailler")->live(),
                        Toggle::make('ravitailler')->label("A été ravitailler")->visible(function(Get $get){
                            return  $get('a_travailler');
                        })->live(),

                        TextInput::make("montant_ravitailler")->numeric()->label("Montant ravitaillé")->suffix('FCFA')
                            ->visible(function(Get $get){
                                return  $get('ravitailler');
                            })
                            ->required(function(Get $get){
                                return  $get('ravitailler');
                            }),
                        Select::make('chauffeur')
                            ->relationship('chauffeurs','id')
                            ->getOptionLabelFromRecordUsing(fn (Chauffeur $record): string => " {$record->nom}  {$record->prenom}  ({$record->phone}) ")
                            ->searchable()->preload()
                            ->visible(function(Get $get){
                                return  $get('a_travailler');
                            })
                            ->required(function(Get $get){
                                return  $get('a_travailler');
                            }),
                        DatePicker::make('date')->required(),
                        TimePicker::make('heure_sortie')->label("Heure de sortie")
                            ->visible(function(Get $get){
                                return  $get('a_travailler');
                            })
                            ->required(function(Get $get){
                                return  $get('a_travailler');
                            })
                            ->live(),
                        TimePicker::make('heure_retour')->label("Heure de retour")
                            ->visible(function(Get $get){
                                return  $get('a_travailler');
                            })
                            ->live(),
                        Textarea::make('observation')
                    ])->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('machine')->searchable()->sortable()->weight(FontWeight::Bold),
                TextColumn::make('locations.client')->searchable()->sortable()->badge()->color('success'),
                TextColumn::make('chauffeurs.nom')->searchable()->sortable(),
                TextColumn::make('date')->searchable()->sortable()->badge()->color('warning'),
                TextColumn::make('heure_sortie')->label('Heure de sortie'),
                TextColumn::make('heure_retour')->label('Heure de retour'),
                TextColumn::make('heures_travaillees')
                    ->label('Heures travaillées')
                    ->state(function ($record): string {
                        if (!$record->heure_sortie || !$record->heure_retour) {
                            return 'N/A';
                        }

                        $sortie = Carbon::parse($record->heure_sortie);
                        $retour = Carbon::parse($record->heure_retour);

                        if ($retour->lt($sortie)) {
                            $retour->addDay();
                        }

                        $heures = $sortie->diffInHours($retour);
                        $minutes = $sortie->diffInMinutes($retour) % 60;

                        return round($heures, 0) . 'h ' . $minutes . 'm';
                    }),
                TextColumn::make('heures_restantes')
                    ->label('Heures restantes')
                    ->state(function ($record): string {
                        if (!$record->locations) {
                            return 'N/A';
                        }

                        $location = $record->locations;

                        // Récupérer la durée spécifique à la machine depuis l'array details
                        $duree_machine = 0;
                        foreach ($location->details as $detail) {
                            $machine = Machine::where('designation', $record->machine)->first();
                            if ($detail['machine'] == $machine->id) {
                                $duree_machine = $detail['duree'];
                                break;
                            }
                        }

                        if ($duree_machine == 0) {
                            return 'N/A';
                        }

                        // Calculer le nombre d'heures travaillées pour cette machine
                        $heures_travailles = 0;
                        $pointages = PointageMachine::where('machine', $record->machine)
                            ->where('location', $location->id)
                            ->where('a_travailler', true)
                            ->get();

                        foreach ($pointages as $pointage) {
                            if ($pointage->heure_sortie && $pointage->heure_retour) {
                                $sortie = Carbon::parse($pointage->heure_sortie);
                                $retour = Carbon::parse($pointage->heure_retour);

                                if ($retour->lt($sortie)) {
                                    $retour->addDay();
                                }

                                $heures = $sortie->diffInHours($retour);
                                $minutes = $sortie->diffInMinutes($retour) % 60;
                                $heures_travailles += $heures + ($minutes / 60);
                            }
                        }

                        // Calculer les heures restantes
                        $heures_restantes = $duree_machine - $heures_travailles;

                        if ($heures_restantes <= 0) {
                            return '0h 0m';
                        }

                        $heures = floor($heures_restantes);
                        $minutes = round(($heures_restantes - $heures) * 60);

                        return $heures . 'h ' . $minutes . 'm';
                    })
                    ->badge()
                    ->color(fn (string $state): string =>
                        match (true) {
                            $state === '0h 0m' => 'danger',
                            $state === 'N/A' => 'gray',
                            default => 'success',
                        }
                    ),
                ToggleColumn::make('a_travailler')->label("A travailler")->disabled(),
                ToggleColumn::make('ravitailler')->label("A été ravitailler")->disabled(),
                TextColumn::make('terminer')->visible(true)

            ])
            ->filters([
                SelectFilter::make('location')
                    ->relationship('locations','id')
                    ->getOptionLabelFromRecordUsing(fn (LocationMachine $record): string => "Location de {$record->client} du {$record->date_debut} pour {$record->duree} jours")
                    ->searchable()
                    ->preload(),
                    DateRangeFilter::make('date')
                    ->startDate(Carbon::now()->startOfYear())
                    ->endDate(Carbon::now()->endOfYear()),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                   // Tables\Actions\DeleteBulkAction::make(),

                    BulkAction::make('éditer')
                        ->label("Editer")
                        ->icon("heroicon-o-printer")
                        ->action(function (Collection $records, $livewire) {
                            try {
                                Log::info('Début de la génération du PDF');
                                Log::info('Filtres:', $livewire->tableFilters);

                                $query = static::getEloquentQuery();
                                $filter = $livewire->tableFilters;

                                return self::generatePdf($filter);
                            } catch (\Exception $e) {
                                Log::error('Erreur lors de la génération du PDF: ' . $e->getMessage());
                                Log::error($e->getTraceAsString());
                                throw $e;
                            }
                        })
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
            'index' => Pages\ListPointageMachines::route('/'),
            'create' => Pages\CreatePointageMachine::route('/create'),
            'view' => Pages\ViewPointageMachine::route('/{record}'),
            'edit' => Pages\EditPointageMachine::route('/{record}/edit'),
        ];
    }


    public static function generatePdf(array $records)
    {
        try {
            Log::info('Début de generatePdf avec les filtres:', $records);

            $filename = "pointage" . time() . ".pdf";

            $date = $records['date']['date'] ?? null;
            if (!$date) {
                Log::error('Aucune date trouvée dans les filtres');
                throw new \Exception('Aucune date sélectionnée');
            }

            $date_debut = strtok($date, ' -');
            $date_fin = substr($date, strpos($date, "- ") + 1);

            $new_date_debut = DateTime::createFromFormat("d/m/Y", $date_debut)->format('Y-m-d');
            $new_date_fin = DateTime::createFromFormat("d/m/Y", trim($date_fin))->format('Y-m-d');

            $location_id = $records['location']['value'] ?? null;
            if (!$location_id) {
                Log::error('Aucune location trouvée dans les filtres');
                throw new \Exception('Aucune location sélectionnée');
            }

            $location = LocationMachine::with('machines')->find($location_id);
            if (!$location) {
                Log::error('Location non trouvée avec l\'ID: ' . $location_id);
                throw new \Exception('Location non trouvée');
            }

            $client = \App\Models\Client::where('designation', $location->client)->first();
            if (!$client) {
                Log::error('Client non trouvé pour la location: ' . $location->client);
                throw new \Exception('Client non trouvé');
            }

            $filename = "pointage {$location->client} du {$location->date_debut}" . time() . ".pdf";

            // Récupérer les pointages pour la période et les camions de la location
            $pointages = PointageMachine::whereBetween('date', [$new_date_debut, $new_date_fin])
                ->whereIn('machine', $location->machines->pluck('designation'))
                ->get();

            Log::info('Nombre de pointages trouvés: ' . $pointages->count());

            // Calculer les jours de travail pour chaque camion
            $statistiques = [];
            foreach ($location->machines as $machine) {
                $statistiques[$machine->designation] = [
                    'heure_travailles' => 0,
                    'heure_restantes' => 0,
                    'total_ravitailler' => 0
                ];
            }

            foreach ($pointages as $pointage) {
                if ($pointage->a_travailler && $pointage->heure_sortie && $pointage->heure_retour) {
                    $sortie = Carbon::parse($pointage->heure_sortie);
                    $retour = Carbon::parse($pointage->heure_retour);

                    if ($retour->lt($sortie)) {
                        $retour->addDay();
                    }

                    $heures = $sortie->diffInHours($retour);
                    $minutes = $sortie->diffInMinutes($retour) % 60;

                    $statistiques[$pointage->machine]['heure_travailles'] += $heures + ($minutes / 60);
                }
                if ($pointage->ravitailler) {
                    $statistiques[$pointage->machine]['total_ravitailler'] += $pointage->qte_ravitailler;
                }
            }

            // Calculer les heures restantes pour chaque machine
            foreach ($statistiques as $machine => $stats) {
                $duree_machine = 0;
                foreach ($location->details as $detail) {
                    $mach = Machine::where('designation', $machine)->first();
                    if ($detail['machine'] == $mach->id) {
                        $duree_machine = $detail['duree'];
                        break;
                    }
                }
                $statistiques[$machine]['heure_restantes'] = $duree_machine - $stats['heure_travailles'];
            }

            // Convertir les données en UTF-8
            $pointages = $pointages->map(function($pointage) {
                $pointage->date = iconv('UTF-8', 'ASCII//TRANSLIT', $pointage->date);
                $pointage->machine = iconv('UTF-8', 'ASCII//TRANSLIT', $pointage->machine);
                if ($pointage->chauffeurs) {
                    $pointage->chauffeurs->nom = iconv('UTF-8', 'ASCII//TRANSLIT', $pointage->chauffeurs->nom);
                    $pointage->chauffeurs->prenom = iconv('UTF-8', 'ASCII//TRANSLIT', $pointage->chauffeurs->prenom);
                }
                return $pointage;
            });

            $client->designation = iconv('UTF-8', 'ASCII//TRANSLIT', $client->designation);
            $client->phone = iconv('UTF-8', 'ASCII//TRANSLIT', $client->phone);
            $client->email = iconv('UTF-8', 'ASCII//TRANSLIT', $client->email);

            $location->date_debut = iconv('UTF-8', 'ASCII//TRANSLIT', $location->date_debut);

            $pdf = Pdf::loadView('pdf.export2', [
                'records' => $pointages,
                'statistiques' => $statistiques,
                'client' => $client,
                'location' => $location,
                'date' => now()->format('d/m/Y H:i'),
                'filename' => $filename,
            ]);

            // Configuration du PDF
            $pdf->setPaper('A4');
            $pdf->setOption('isHtml5ParserEnabled', true);
            $pdf->setOption('isRemoteEnabled', true);
            $pdf->setOption('isPhpEnabled', true);
            $pdf->setOption('isFontSubsettingEnabled', true);
            $pdf->setOption('defaultFont', 'Arial');

            Log::info('PDF généré avec succès');
            return response()->streamDownload(function() use ($pdf) {
                echo $pdf->output();
            }, $filename, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="'.$filename.'"',
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur dans generatePdf: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            throw $e;
        }
    }

}
