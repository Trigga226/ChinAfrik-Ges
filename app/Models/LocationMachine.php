<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LocationMachine extends Model
{
    protected $fillable = [
        "client",
        "date",
        "remise",
        "total_a_percevoir",
        "statut",
        "details",
    ];

    public function clients()
    {
        return $this->belongsTo(Client::class, 'client','designation');
    }

    public function machines()
    {
        return $this->belongsToMany(Machine::class, 'location_machines_machines','location_id','machine_id')->withTimestamps()->withPivot('location_id');
    }



    protected $casts=[
        'machine'=>'array',
        'details'=>'array',
    ];

    public function pointages(){
        return $this->hasMany(PointageMachine::class,'location','id');
    }

}
