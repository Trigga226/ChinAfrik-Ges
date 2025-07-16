<?php

namespace App\Filament\Cam\Pages;

use App\Models\Camion;
use App\Models\Machine;
use App\Models\SuiviCamion;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;

use App\Models\SuiviMachine;

class ImpressionSuivis extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-printer';

    protected static string $view = 'filament.cam.pages.impression-suivis';

    protected static ?string $navigationGroup = 'Suivis';

    public ?string $camion = null;
    public ?string $camion_start_date = null;
    public ?string $camion_end_date = null;

    public ?string $machine = null;
    public ?string $machine_start_date = null;
    public ?string $machine_end_date = null;


    public function mount(): void
    {
        $this->form->fill([
            'camion_start_date' => null,
            'camion_end_date' => null,
            'machine_start_date' => null,
            'machine_end_date' => null,
        ]);
    }

    protected function getFormSchema(): array
    {
        return [
            Grid::make(2) // Empiler les sections verticalement
                ->schema([
                    Section::make('Impressions des Camions')
                        ->schema([
                            Select::make('camion')
                                ->label('Sélectionner un camion')
                                ->options(Camion::query()->pluck('designation', 'immatriculation'))
                                ->searchable()
                                ->live(),
                            DatePicker::make('camion_start_date')
                                ->label('Date de début')
                                ->required(),
                            DatePicker::make('camion_end_date')
                                ->label('Date de fin')
                                ->required(),
                            Placeholder::make('camion_buttons')
                                ->label('')
                                ->content(view('filament.cam.pages.partials.camion-buttons')),
                        ])->columnSpan(1),
                    Section::make('Impressions des Machines')
                        ->schema([
                            Select::make('machine')
                                ->label('Sélectionner une immatriculation machine')
                                ->options(Machine::query()->pluck('immatriculation', 'immatriculation'))
                                ->searchable()
                                ->live(),
                             DatePicker::make('machine_start_date')
                                ->label('Date de début')
                                ->required(),
                            DatePicker::make('machine_end_date')
                                ->label('Date de fin')
                                ->required(),
                            Placeholder::make('machine_buttons')
                                ->label('')
                                ->content(view('filament.cam.pages.partials.machine-buttons')),
                        ])->columnSpan(1),
                ])
        ];
    }

    public function imprimerCamionSelectionne()
    {
        $suivis = SuiviCamion::where('camion', $this->camion)->orderBy('date')->get();
        $camion = Camion::where('immatriculation', $this->camion)->first();

        $data = [
            'suivis' => $suivis,
            'camion' => $camion,
            'date'   => now()->format('d/m/Y')
        ];


        $pdf = Pdf::loadView('pdf.suivi-camion', $data)
            ->setPaper('a4', 'landscape');

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->stream();
        }, 'suivi-' . $this->camion . '.pdf');
    }

    public function imprimerTousLesCamions()
    {
        $suivisCollection = SuiviCamion::whereBetween('date', [$this->camion_start_date, $this->camion_end_date])
            ->with(['camions', 'chauffeurs'])
            ->get();

        // Filtrer les suivis qui n'ont pas de camion associé pour éviter les erreurs
        $suivis = $suivisCollection->filter(function ($suivi) {
            return $suivi->camion !== null;
        })->groupBy('camion.immatriculation');


        $cams=[];
        foreach ($suivis as $suivi){
            for ($i=0; $i<count($suivi); $i++){
              //  @dd($suivi[$i]->camion);
             //   $cami=Camion::where('immatriculation', $sui->camion)->first();
                $cams[]=$suivi[$i]->camion;
            }

        }
        $cams=array_unique($cams);

        $data = [
            'suivisGroupes' => $suivis,
            'startDate' => $this->camion_start_date,
            'endDate' => $this->camion_end_date,
            'date' => now()->format('d/m/Y'),
            'camions' => $cams
        ];

        $pdf = Pdf::loadView('pdf.suivi-tous-camions', $data)
            ->setPaper('a4', 'landscape');

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->stream();
        }, 'suivi-tous-camions-' . $this->camion_start_date . '-' . $this->camion_end_date . '.pdf');
    }

    public function imprimerMachineSelectionnee()
    {
        $suivis = SuiviMachine::where('machine', $this->machine)->orderBy('date')->get();
        $machine = Machine::where('immatriculation', $this->machine)->first();


        $data = [
            'suivis' => $suivis,
            'machine' => $machine,
            'date'   => now()->format('d/m/Y')
        ];


        $pdf = Pdf::loadView('pdf.suivi-machine', $data)
            ->setPaper('a4', 'landscape');

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->stream();
        }, 'suivi-' . $this->machine . '.pdf');
    }

    public function imprimerToutesLesMachines()
    {
        $suivis = SuiviMachine::whereBetween('date', [$this->machine_start_date, $this->machine_end_date])
            ->with('machines') // Eager load machine info
            ->get()
            ->groupBy('machine.designation'); // Group by machine designation

        $macs=[];
        foreach ($suivis as $suivi){
            for ($i=0; $i<count($suivi); $i++){
                //  @dd($suivi[$i]->camion);
                //   $cami=Camion::where('immatriculation', $sui->camion)->first();
                $macs[]=$suivi[$i]->machine;
            }

        }
        $macs=array_unique($macs);


        $data = [
            'suivisGroupes' => $suivis,
            'startDate' => $this->machine_start_date,
            'endDate' => $this->machine_end_date,
            'date' => now()->format('d/m/Y'),
            'machines' => $macs
        ];

        $pdf = Pdf::loadView('pdf.suivi-toutes-machines', $data)
            ->setPaper('a4', 'landscape');

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->stream();
        }, 'suivi-toutes-machines-' . $this->machine_start_date . '-' . $this->machine_end_date . '.pdf');
    }
}
