<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DossierPostulant extends Model
{
    //

    protected $fillable = [
        "postulant_id",
        "nom_complet",
        "bourse",
        "email",
        "phone",
        "photo",
        "date_naissance",
        "numero_passeport",
        "scann_passeport",
        "date_delivrance_passport",
        "date_expiration_passport",
        "numero_cnib",
        "scann_cnib",
        "date_delivrance_cnib",
        "date_expiration_cnib",
        "pays",
        "ville",
        "secteur",
        "documents",
        "complet",
        "etat",
        "etapes",
        "type",
    ];


    protected $casts=[
        "documents"=>"array",
        "etapes"=>"array",
    ];

    public function postulants()
    {
        return $this->hasOne(Postulant::class, 'id','postulant_id');
    }

    public function bourses()
    {
        return $this->belongsTo(Bourse::class, 'bourse','titre');
    }

    public function versements()
    {
        return $this->hasMany(Versement::class, 'dossier_id','id');
    }
}
