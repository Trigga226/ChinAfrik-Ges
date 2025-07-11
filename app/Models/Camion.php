<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Camion extends Model
{
    protected $fillable = [
        "designation",
        "immatriculation",
        "categorie",
        "marque",
        "date_mise_en_service",
        "status",
        "observation",
        "cout",
    ];

    public function categories(){
        return $this->belongsTo(CategorieCamion::class, 'categorie','designation');
    }

    public function locations()
    {
        return $this->belongsToMany(LocationCamion::class, 'location_camion_camions','camion_id','location_id')->withTimestamps()->withPivot('camion_id');
    }

    public function pointages(){
        return $this->hasMany(PointageCamion::class,'camion','designation');
    }

}
