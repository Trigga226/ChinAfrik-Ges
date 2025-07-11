<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Versement extends Model
{
    protected $fillable = [
        "dossier_id",
        "reference",
        "montant",
        "date_versement",
        "motif",
        "moyen_versement",
    ];



    public function dossiers()
    {
        return $this->belongsTo(DossierPostulant::class, 'dossier_id','id');
    }
}
