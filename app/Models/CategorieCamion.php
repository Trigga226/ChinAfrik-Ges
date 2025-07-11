<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CategorieCamion extends Model
{
    protected $fillable = [
        "designation",
        "description"
    ];

    public function camions(){
        return $this->hasMany(Camion::class, 'categorie','designation');
    }
}
