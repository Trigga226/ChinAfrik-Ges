<?php

namespace App\Filament\Cam\Resources;

use App\Filament\Cam\Resources\PointageCamionResource\Pages;
use App\Filament\Cam\Resources\PointageCamionResource\RelationManagers;
use App\Models\Camion;
use App\Models\Chauffeur;
use App\Models\LocationCamion;
use App\Models\PointageCamion;
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
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Columns\Column;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use http\Client;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Collection;
use phpDocumentor\Reflection\Location;
use Tapp\FilamentValueRangeFilter\Filters\ValueRangeFilter;
use Malzariey\FilamentDaterangepickerFilter\Filters\DateRangeFilter;
use Malzariey\FilamentDaterangepickerFilter\Fields\DateRangePicker;
use React\Dns\Model\Record;
use Illuminate\Support\Facades\Log;

class PointageCamionResource extends Resource
{
    protected static ?string $model = PointageCamion::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup="Gestion des camions";
    protected static ?string $label="Pointage des camions";
    protected static ?string $pluralLabel="Pointage des camions";

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                ->schema([
                    Select::make('location')
                    ->relationship('locations','id')
                    ->getOptionLabelFromRecordUsing(fn (LocationCamion $record): string => "Location de {$record->client} du {$record->date_debut} pour {$record->duree} jours")
                    ->searchable()
                    ->required()
                    ->preload()
                    ->live()
                    ->afterStateUpdated(function ($state) {
                        if ($state) {
                            $location = LocationCamion::find($state);
                            if ($location) {
                                self::verifierEtMettreAJourStatutLocation($location);
                            }
                        }
                    }),
                    Select::make('camion')
                    ->options(function (Get $get): Collection {
                        $locationId = $get('location');

                        if (!$locationId) {
                            return Collection::empty();
                        }

                        // For a many-to-many relationship, we need to query through the relationship
                        return Camion::query()
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
                    TextInput::make("montant_ravitailler")->numeric()->label("Montant ravitaillement")->suffix('FCFA')
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
                    TimePicker::make('heure_sortie')->label("Heure de sorite")
                    ->visible(function(Get $get){
                        return  $get('a_travailler');
                    })
                    ->required(function(Get $get){
                        return  $get('a_travailler');
                    }),
                    TimePicker::make('heure_retour')->label("Heure de retour")
                    ->visible(function(Get $get){
                        return  $get('a_travailler');
                    }),
                    Textarea::make('observation')
                ])->columns(3),
            ]);
    }

    /**
     * @throws \Exception
     */
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('camion')->searchable()->sortable()->weight(FontWeight::Bold),
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





                        return round($heures,0) . 'h ' . $minutes . 'm';
                    }),
                TextColumn::make('jours_restants')
                    ->label('Jours restants')
                    ->state(function ($record): string {
                        if (!$record->locations) {
                            return 'N/A';
                        }

                        $location = $record->locations;
                        $date_debut = Carbon::parse($location->date);
                        $aujourdhui = Carbon::now();



                        // Récupérer la durée spécifique au camion depuis l'array details
                        $duree_camion = 0;
                        foreach ($location->details as $detail) {
                            $cam=Camion::where('designation',$record->camion)->first();

                            if ($detail['camion'] == $cam->id) {
                                $duree_camion = $detail['duree'];

                                break;
                            }
                        }

                        if ($duree_camion == 0) {
                            return 'N/A';
                        }

                        // Calculer le nombre de jours travaillés pour ce camion
                        $jours_travailles = PointageCamion::where('camion', $record->camion)
                            ->where('location', $location->id)
                            ->where('a_travailler', true)
                            ->count();



                        // Calculer le nombre de jours écoulés depuis le début
                        $jours_ecoules = $aujourdhui->diffInDays($date_debut);


                        // Calculer les jours restants en tenant compte de la durée spécifique au camion
                        $jours_restants = $duree_camion - $jours_travailles;

                        if ($jours_restants <= 0) {
                            return '0';
                        }

                        return $jours_restants;
                    })
                    ->badge()
                    ->color(fn (string $state): string =>
                        match (true) {
                            $state === '0' => 'danger',
                            $state === 'N/A' => 'gray',
                            default => 'success',
                        }
                    ),
                ToggleColumn::make('a_travailler')->label("A travailler")->disabled(),
                ToggleColumn::make('ravitailler')->label("A été ravitailler")->disabled(),
            ])
            ->filters([
                SelectFilter::make('location')
                    ->relationship('locations','id')
                    ->getOptionLabelFromRecordUsing(fn (LocationCamion $record): string => "Location de {$record->client} du {$record->date_debut} datant du {$record->created_at} ")
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
            'index' => Pages\ListPointageCamions::route('/'),
            'create' => Pages\CreatePointageCamion::route('/create'),
            'view' => Pages\ViewPointageCamion::route('/{record}'),
            'edit' => Pages\EditPointageCamion::route('/{record}/edit'),
        ];
    }


    /**
     * @throws \Exception
     */
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

            $location = LocationCamion::with('camions')->find($location_id);
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
            $pointages = PointageCamion::whereBetween('date', [$new_date_debut, $new_date_fin])
                ->whereIn('camion', $location->camions->pluck('designation'))
                ->get();

            Log::info('Nombre de pointages trouvés: ' . $pointages->count());

            // Calculer les jours de travail pour chaque camion
            $statistiques = [];
            foreach ($location->camions as $camion) {
                $statistiques[$camion->designation] = [
                    'jours_travailles' => 0,
                    'jours_restants' => 0,
                    'total_ravitailler' => 0
                ];
            }

            foreach ($pointages as $pointage) {
                if ($pointage->a_travailler) {
                    $statistiques[$pointage->camion]['jours_travailles']++;
                }
                if ($pointage->ravitailler) {
                    $statistiques[$pointage->camion]['total_ravitailler'] += $pointage->montant_ravitailler;
                }
            }

            // Calculer les jours restants pour chaque camion
            foreach ($statistiques as $camion => $stats) {
                $duree_camion = 0;
                foreach ($location->details as $detail) {
                    $cam = Camion::where('designation', $camion)->first();
                    if ($detail['camion'] == $cam->id) {
                        $duree_camion = $detail['duree'];
                        break;
                    }
                }
                $statistiques[$camion]['jours_restants'] = $duree_camion - $stats['jours_travailles'];
            }

            // Convertir les données en UTF-8
            $pointages = $pointages->map(function($pointage) {
                $pointage->date = iconv('UTF-8', 'ASCII//TRANSLIT', $pointage->date);
                $pointage->camion = iconv('UTF-8', 'ASCII//TRANSLIT', $pointage->camion);
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

            $pdf = Pdf::loadView('pdf.export', [
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

    public function rapportCamionsParStatut($date = null)
    {
        // Utiliser la date fournie ou aujourd'hui
        $date = $date ?? now()->toDateString();

        // Camions qui ont travaillé à cette date
        $camionsQuiOntTravaille = Camion::whereHas('pointages', function ($query) use ($date) {
            $query->where('a_travailler', true)
                ->whereBetween('date_travail', $date);
        })->get();

        // Camions qui ont été pointés comme n'ayant pas travaillé à cette date
        $camionsQuiNontPasTravaille = Camion::whereHas('pointages', function ($query) use ($date) {
            $query->where('a_travailler', false)
                ->whereDate('created_at', $date);
        })->get();

        // Camions qui n'ont pas été pointés du tout à cette date
        $camionsNonPointes = Camion::whereDoesntHave('pointages', function ($query) use ($date) {
            $query->whereDate('created_at', $date)
                ->orWhereDate('date_travail', $date);
        })->get();

        return [
            'ont_travaille' => $camionsQuiOntTravaille,
            'nont_pas_travaille' => $camionsQuiNontPasTravaille,
            'non_pointes' => $camionsNonPointes,
        ];
    }

    private static function verifierEtMettreAJourStatutLocation(LocationCamion $location)
    {
        $tousCamionsTermines = true;
        $camions = $location->camions;

        // Vérifier si tous les camions de la location ont été pointés
        $camionsPointes = PointageCamion::where('location', $location->id)
            ->distinct('camion')
            ->pluck('camion')
            ->toArray();

        foreach ($camions as $camion) {
            // Vérifier si le camion a été pointé
            if (!in_array($camion->designation, $camionsPointes)) {
                $tousCamionsTermines = false;
                break;
            }

            $duree_camion = 0;
            foreach ($location->details as $detail) {
                $cam = Camion::where('designation', $camion->designation)->first();
                if ($detail['camion'] == $cam->id) {
                    $duree_camion = $detail['duree'];
                    break;
                }
            }

            $jours_travailles = PointageCamion::where('camion', $camion->designation)
                ->where('location', $location->id)
                ->where('a_travailler', true)
                ->count();

            $jours_restants = $duree_camion - $jours_travailles;

            if ($jours_restants > 0) {
                $tousCamionsTermines = false;
                break;
            }
        }

        if ($tousCamionsTermines && $location->statut !== 'Terminer') {
            $location->update(['statut' => 'Terminer']);
        }
    }

}

