<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PointageCamion extends Model
{
    protected $fillable=[
        "camion",
        "location",
        "chauffeur",
        "date",
        "heure_sortie",
        "heure_retour",
        "ravitailler",
        "montant_ravitailler",
        "a_travailler",
        "observation",
    ];



    public function camions(){
        return $this->belongsTo(Camion::class,'camion','designation');
    }


    public function locations(){
        return $this->belongsTo(LocationCamion::class,'location','id');
    }

    public function chauffeurs(){
        return $this->belongsTo(Chauffeur::class,'chauffeur','id');
    }
}
