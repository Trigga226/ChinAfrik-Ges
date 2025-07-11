<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PointageMachine extends Model
{
    protected $fillable=[
        "machine",
        "location",
        "chauffeur",
        "date",
        "heure_sortie",
        "heure_retour",
        "ravitailler",
        "qte_ravitailler",
        "a_travailler",
        "observation",
    ];



    public function machines(){
        return $this->belongsTo(Machine::class,'machine','designation');
    }


    public function locations(){
        return $this->belongsTo(LocationMachine::class,'location','id');
    }

    public function chauffeurs(){
        return $this->belongsTo(Chauffeur::class,'chauffeur','id');
    }
}
