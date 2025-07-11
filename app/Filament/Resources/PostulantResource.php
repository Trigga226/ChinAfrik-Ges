<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PostulantResource\Pages;
use App\Filament\Resources\PostulantResource\RelationManagers;
use App\Models\DossierPostulant;
use App\Models\Postulant;
use App\Models\User;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\SubNavigationPosition;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;

class PostulantResource extends Resource
{
    protected static ?string $model = Postulant::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationGroup="Gestion des Bourses";

    protected static ?string $recordTitleAttribute = 'nom_complet';

    public static function getGloballySearchableAttributes(): array
    {
        return ['nom_complet', 'email','phone',];
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'warning';
    }

    public static function getGlobalSearchResultDetails(\Illuminate\Database\Eloquent\Model $record): array
    {
        return [
            'Nom complet' => $record->nom_complet,
            'E-mail' => $record->email,
            'Téléphone' => $record->phone,
        ];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                ->schema([
                    FileUpload::make('photo')->avatar()
                    ->disk('public')
                    ->directory('photos_postulants')
                    ->downloadable()
                    ->image()
                ]),
                Forms\Components\Section::make()
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('nom_complet')->label('Nom complet')->required()->placeholder('Nom complet')->prefixIcon("heroicon-o-user"),
                    Forms\Components\TextInput::make('email')->label('E-mail')->required()->placeholder('E-mail')->prefixIcon("heroicon-o-envelope")->email()->unique(ignoreRecord: true),
                    PhoneInput::make('phone')->label('Téléphone')->required()->placeholder('Téléphone')->prefixIcon("heroicon-o-phone")->unique(ignoreRecord: true),
                    Select::make('genre')->options([
                        "Homme" => "Homme",
                        "Femme" => "Femme",
                    ])->searchable()->label('Genre')->required(),
                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('photo')->circular()->defaultImageUrl('/logo.png'),
                Tables\Columns\TextColumn::make('nom_complet')->label('Nom complet')->searchable()->sortable()->weight(FontWeight::Bold),
                Tables\Columns\TextColumn::make('email')->label('E-mail')->searchable()->sortable()->badge()->color('info'),
                Tables\Columns\TextColumn::make('phone')->label('Téléphone')->searchable()->sortable()->badge()->color('success'),
                Tables\Columns\TextColumn::make('genre')->label('Genre')->searchable()->sortable()->badge()->color('warning'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                ->modal(),
                Tables\Actions\EditAction::make()
                ->modal()->color('warning')
                ->after(function (Postulant $record,Tables\Actions\EditAction $action) {


                    try {
                        $user=User::where('email',$record->email)->first();
                        $dossier=DossierPostulant::where('email',$record->email)->first();

                        if (!is_null($user)){
                            $user->name=$record->nom_complet;
                            $user->email=$record->email;
                            $user->phone=str_replace('+','',$record->phone);
                            $user->genre=$record->genre;
                            $user->avatar_url=$record->photo;
                            $user->save();
                        }

                        if (!is_null($dossier)){
                            $dossier->nom_complet=$record->nom_complet;
                            $dossier->email=$record->email;
                            $dossier->phone=str_replace('+','',$record->phone);
                            $dossier->photo=$record->photo;
                            $dossier->save();
                        }
                        $record->assignRole('postulant');
                    }catch (\PDOException $exception){
                        Notification::make()
                            ->title('Erreur')
                            ->danger()
                            ->body("Une erreure s'est produite. Vérifiez les informations entrées")
                            ->send();
                        $action->cancel();
                    }
                }),
                Tables\Actions\DeleteAction::make('Suprimer')
                ->after(function (Postulant $record,Tables\Actions\DeleteAction $action) {

                    try {
                        $user=User::where('email',$record->email)->first();
                        $dossier=DossierPostulant::where('email',$record->email)->first();

                        if (!is_null($user)){
                            $user->delete();
                        }

                        if (!is_null($dossier)){
                            $dossier->delete();
                        }
                    }catch (\PDOException $exception){
                        Notification::make()
                            ->title('Erreur')
                            ->danger()
                            ->body("Une erreure s'est produite. Vérifiez les informations entrées")
                            ->send();
                        $action->cancel();
                    }
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
            RelationManagers\DossiersRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPostulants::route('/'),
          //  'create' => Pages\CreatePostulant::route('/create'),
          //  'view' => Pages\ViewPostulant::route('/{record}'),
          //  'edit' => Pages\EditPostulant::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
}
