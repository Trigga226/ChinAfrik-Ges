<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SuiviMachine extends Model
{
protected $fillable = [
"date",
"machine",
"chauffeur",
"type_entretient",
"piece_change",
"decription_panne",
"kilometrage",
"duree_immobilisation",
"atelier",
"observation",
"document",
];

    public function machines():BelongsTo{
        return $this->belongsTo(Machine::class,'machine','immatriculation');
    }

    public function chauffeurs():BelongsTo{
        return $this->belongsTo(Chauffeur::class,'chauffeur','id');
    }
}
