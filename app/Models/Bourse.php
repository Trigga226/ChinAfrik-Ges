<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bourse extends Model
{
    protected $fillable = [
        "titre",
        "description",
        "coutt",
        "coutp",
        "frais",
        "requis",
    ];

    protected $casts=[
        "requis"=>"array",
    ];

    public function dossiers()
    {
        return $this->hasMany(DossierPostulant::class, 'bourse','titre');
    }
}
