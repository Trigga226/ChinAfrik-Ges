<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Postulant extends Model
{
    protected $primaryKey='id';
    protected $fillable = [
        'nom_complet',
        'email',
        'phone',
        'photo',
        'genre',
    ];


    public function dossiers()
    {
        return $this->hasOne(DossierPostulant::class, 'id','postulant_id');
    }
}
