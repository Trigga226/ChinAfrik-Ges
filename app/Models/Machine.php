<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Machine extends Model
{
    protected $fillable = [
        "designation",
        "immatriculation",
        "categorie",
        "marque",
        "date_mise_en_service",
        "status",
        'cout',
        "observation",
    ];

    public function categories(){
        return $this->belongsTo(CategorieMachine::class, 'categorie','designation');
    }

    public function locations()
    {
        return $this->belongsToMany(LocationMachine::class, 'location_machines_machines','machine_id','location_id')->withTimestamps()->withPivot('camion_id');
    }

    public function pointages(){
        return $this->hasMany(PointageMachine::class,'machine','designation');
    }
}
