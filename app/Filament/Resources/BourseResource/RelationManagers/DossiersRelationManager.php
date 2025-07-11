<?php

namespace App\Filament\Resources\BourseResource\RelationManagers;

use App\Models\Bourse;
use App\Models\DossierPostulant;
use App\Models\Postulant;
use App\Models\Versement;
use DateTime;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\HtmlString;

class DossiersRelationManager extends RelationManager
{
    protected static string $relationship = 'dossiers';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Wizard::make()
                    ->schema([
                        Forms\Components\Wizard\Step::make('Postulant')
                            ->description('Informations sur le postulant')
                            ->schema([

                                Forms\Components\Select::make('bourse')
                                    ->relationship('bourses','titre')
                                    ->searchable()
                                    ->preload()
                                    ->live(),
                                Forms\Components\Select::make('postulant_id')->label('Postulant')->disabledOn('edit')
                                    ->options(Postulant::all()->pluck('nom_complet', 'id'))->prefixIcon("heroicon-o-user")
                                    ->required()
                                    ->preload()
                                    ->searchable()
                                    ->live()
                                    ->afterStateUpdated(function (Set $set, $state) {
                                        $postulant=Postulant::find($state);
                                        $set('nom_complet', $postulant->nom_complet);
                                        $set('email', $postulant->email);
                                        $set('phone', $postulant->phone);
                                    }),
                                Forms\Components\TextInput::make('nom_complet')->label('Nom Complet')->required()->prefixIcon("heroicon-o-user")->readonly(),
                                Forms\Components\TextInput::make('email')->label('E-mail')->required()->prefixIcon("heroicon-o-envelope")->readOnly()->email()->unique(ignoreRecord: true),
                                Forms\Components\TextInput::make('phone')->label('Téléphone')->required()->prefixIcon("heroicon-o-phone")->readOnly()->tel()->unique(ignoreRecord: true),
                                Forms\Components\DatePicker::make('date_naissance')->label('Date de naissance')->required()->prefixIcon("heroicon-o-calendar")
                                    ->live()->reactive()
                                    ->afterStateUpdated(function (Set $set, $state,Forms\Get  $get)  {

                                        $annee=new DateTime(date("Y"));
                                        $date=new DateTime($state);
                                        $age=$annee->diff($date);
                                        $age_requis=0;



                                        $bourse=Bourse::where('titre',$get('bourse'))->first();
                                        foreach ($bourse->requis as $requis){
                                            if (str_starts_with($requis,'Age inferieur a')){
                                                $age_requis=(int)str_replace('Age inferieur a','',$requis);

                                            }
                                        }

                                        if ($age->y > $age_requis){
                                            $set('info',"Age superieur a l'age maximum requis.Cette personne ne peut pas postulé pour cette bourse");
                                            $set('peut',false);


                                        }else{
                                            $set('peut',true);
                                        }

                                    }),
                                Forms\Components\Toggle::make('peut')->label('peut')->visible(false)->default(true)->reactive(),
                                TextInput::make('pays')->label('Pays')->required()->prefixIcon("heroicon-o-globe-alt"),
                                TextInput::make('ville')->label('Ville')->required()->prefixIcon("heroicon-o-map"),
                                TextInput::make('secteur')->label('Secteur')->required()->prefixIcon("heroicon-o-map-pin"),
                                Forms\Components\Placeholder::make('')->extraAttributes(function (Get $get): array {

                                    $dateactu=new DateTime(date('Y'));
                                    $datenaiss= new DateTime($get('date_naissance'));
                                    $age=$dateactu->diff($datenaiss);
                                    $age_requis=0;

                                    $bourse=Bourse::where('titre',$get('bourse'))->first();
                                    if (!is_null($bourse)){
                                        foreach ($bourse->requis as $requis){
                                            if (str_starts_with($requis,'Age inferieur a')){
                                                $age_requis=(int)str_replace('Age inferieur a','',$requis);

                                            }
                                        }
                                    }



                                    if ((int) $age->y > (int) $age_requis){

                                        return ['style' => 'color: red ;'];
                                    }else{
                                        return ['style' => 'color: transparent;'];
                                    }



                                })
                                    ->content(new HtmlString("<label >Age superieur a l'age maximum requis.Cette personne ne peut pas postulé pour cette bourse</label>"))->visible(function (Get $get) {
                                        if ($get('peut')){

                                            return false;
                                        }else{

                                            return true;
                                        }
                                    }),
                            ]),

                        Forms\Components\Wizard\Step::make("Documents d'identité")
                            ->description("Imformation sur les documents d'identié")
                            ->schema([
                                TextInput::make('numero_passeport')->prefixIcon('heroicon-o-identification')->label("Numéro de passport"),
                                DatePicker::make('date_delivrance_passport')->prefixIcon('heroicon-o-calendar')->label("Date de délivrance du passport"),
                                DatePicker::make('date_expiration_passport')->prefixIcon('heroicon-o-calendar')->label("Date d'expiration du passport"),
                                TextInput::make('numero_cnib')->prefixIcon('heroicon-o-identification')->label("Numéro de CNI"),
                                DatePicker::make('date_delivrance_cnib')->prefixIcon('heroicon-o-calendar')->label("Date de délivrance de CNI"),
                                DatePicker::make('date_expiration_cnib')->prefixIcon('heroicon-o-calendar')->label("Date d'expiration de CNI"),
                                FileUpload::make('scann_passeport')->label('Scanne du passeport')->disk('public')->directory('passport')->downloadable()->preserveFilenames(),
                                FileUpload::make('scann_cnib')->label('Scanne de CNI')->disk('public')->directory('cni')->downloadable()->preserveFilenames(),
                            ])->columns(3),

                        Forms\Components\Wizard\Step::make('Supplement')
                            ->description("Supplement et suivis de dossier")
                            ->schema([
                                Toggle::make('complet')->label('Dossier complet'),
                                Select::make('etat')->label('Etat')
                                    ->label("Etat du dossier")
                                    ->options([
                                        "En attente" => "En attente",
                                        "En cours" => "En cours",
                                        "Suspendu" => "Suspendu",
                                        "Rejeter" => "Rejeter",
                                        "Terminer" => "Terminer",
                                    ])->searchable(),
                                Forms\Components\CheckboxList::make('etapes')->label('Etapes')->options([
                                    "Entretient effectué" => "Entretient effectué",
                                    "Dossier soumis" => "Dossier soumis",
                                    "Lettre reçue" => "Lettre reçue",
                                    "Visa accordé" => "Visa accordé",
                                ]) ->gridDirection('row')->columns(4)->bulkToggleable()->columnSpanFull(),

                                FileUpload::make('documents')
                                    ->multiple()->label('Documents')
                                    ->disk('public')
                                    ->directory('documents')
                                    ->panelLayout('grid')
                                    ->preserveFilenames()
                                    ->downloadable()->columnSpanFull(),
                            ])

                    ])->columnSpanFull()->columns(3)->skippable()
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('titre')
            ->columns([
                Tables\Columns\ImageColumn::make('photo')->circular()->defaultImageUrl("/logo.png"),
                TextColumn::make('bourse')->label('Bourse')->searchable()->weight(FontWeight::Bold),
                TextColumn::make('nom_complet')->label('Nom complet')->searchable(),
                TextColumn::make('phone')->label('Phone')->searchable()->badge()->color('success'),
                TextColumn::make('etat')->label('Etat')->searchable()->default('En cours')->color('warning'),
                Tables\Columns\CheckboxColumn::make('complet')->label('Dossier complet')->disabled(),
                TextColumn::make('etapes')->label('Etapes')->searchable()->badge()->color('info')
                    ->state(function (DossierPostulant $dossier){
                        $list=[];
                        $list=$dossier->etapes;

                        if (is_array($list) && !empty($list)) {


                            return end($list);
                        }
                        return null; // ou une valeur par défaut si le tableau est vide ou non valide
                    }),

                TextColumn::make('solde')
                    ->label('Solde á payer')
                    ->state(function (DossierPostulant $dossier){
                        $solde=0;
                        $total=0;

                        if(!is_null($dossier->bourse)){
                            $bourse=Bourse::where('titre',$dossier->bourse)->first();
                            $frais=$bourse->frais;
                            $cout=$bourse->cout;
                            $total=(int)$frais+ (int)$cout;


                            $versement=Versement::where('dossier_id',$dossier->id)->first();


                            if (!is_null($versement)) {

                                $solde=$total-$versement->montant;
                                return $solde;
                            }else{
                                return $total;
                            }
                        }else{
                            return "Merci de choisir une bourse";
                        }

                    })->color(function (string $state): string {
                        if((int)$state<500000 ){
                            return 'success';
                        }
                        if ((int)$state>500000 && (int) $state <1000000){
                            return 'warning';
                        }
                        else{
                            return 'danger';
                        }
                    })->money('XOF', locale: 'fr')->weight(FontWeight::Bold)

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
