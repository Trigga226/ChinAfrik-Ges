<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\DossierPostulant;
use App\Models\Postulant;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $label="Utilisateur";
    protected static ?string $pluralLabel="Utilisateurs";
    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                ->schema([
                    FileUpload::make('avatar_url')
                    ->disk('public')
                    ->directory('avatars')
                    ->avatar()->image()
                ]),
                Forms\Components\Section::make()
                ->schema([
                    Forms\Components\TextInput::make('name')->label("Nom")->required()->prefixIcon('heroicon-o-user'),
                    Forms\Components\TextInput::make('email')->label("E-mail")->required()->prefixIcon('heroicon-o-envelope')->email()->unique(User::class,'email',ignoreRecord: true),
                    PhoneInput::make('phone')->label("Téléphone")->required()->prefixIcon('heroicon-o-phone')->unique(User::class,'phone',ignoreRecord: true),
                    Forms\Components\Select::make('genre')
                    ->options([
                        "Homme" => "Homme",
                        "Femme" => "Femme",
                    ])->searchable()->label("Genre")->required(),
                    Forms\Components\TextInput::make('password')->label("Mot de passe")->required()->prefixIcon('heroicon-o-lock-closed')->password()->revealable()->visibleOn('create'),
                    Forms\Components\Select::make('roles')
                        ->relationship('roles', 'name')
                        ->multiple()
                        ->preload()
                        ->searchable()->prefixIcon('heroicon-o-shield-check'),
                ])->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('avatar_url')->circular()->defaultImageUrl('/logo.png'),
                TextColumn::make('name')->label("Nom")->sortable()->searchable()->weight(FontWeight::Bold),
                TextColumn::make('email')->label("E-mail")->sortable()->searchable()->badge()->color('info'),
                TextColumn::make('phone')->label("Téléphone")->sortable()->searchable()->badge()->color('warning'),
                TextColumn::make('roles.name')->label("Role")->sortable()->searchable()->badge()->color('success'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()->color('warning')
                ->slideOver()
                ->after(function (User $record) {
                    $postulant=Postulant::where('email',$record->email)->first();
                    $dossier=DossierPostulant::where('email',$record->email)->first();
                    if (!is_null($postulant)) {
                        $postulant->nom_complet=$record->name;
                        $postulant->email=$record->email;
                        $postulant->phone=str_replace('+','',$record->phone);
                        $postulant->photo=$record->avatar_url;
                        $postulant->genre=$record->genre;
                        $postulant->save();
                    }

                    if (!is_null($dossier)) {
                        $dossier->nom_complet=$record->name;
                        $dossier->email=$record->email;
                        $dossier->phone=str_replace('+','',$record->phone);
                        $dossier->photo=$record->avatar_url;
                        $dossier->save();
                    }
                }),
                Tables\Actions\DeleteAction::make('Supprimer')
                ->after(function (User $record) {
                    $postulant=Postulant::where('email',$record->email)->first();
                    $dossier=DossierPostulant::where('email',$record->email)->first();

                    if (!is_null($postulant)){
                        $postulant->delete();
                    }
                    if (!is_null($dossier)) {
                        $dossier->delete();
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
            //
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'phone', 'email', 'phone','roles.name'];
    }

    public static function getGlobalSearchResultDetails(\Illuminate\Database\Eloquent\Model $record): array
    {
        return [
            'Nom' => $record->name,
            'E-mail' => $record->email,
            'Phone' => $record->phone,
            'genre' => $record->genre,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
          //  'create' => Pages\CreateUser::route('/create'),
           // 'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
