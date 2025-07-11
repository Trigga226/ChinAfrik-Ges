<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use phpDocumentor\Reflection\Location;

class LocationCamion extends Model
{
    protected $fillable = [
        "client",
        "date",
        "remise",
        "total_a_percevoir",
        "details",
        "statut",
    ];

    public function clients()
    {
        return $this->belongsTo(Client::class, 'client','designation');
    }

    public function camions()
    {
        return $this->belongsToMany(Camion::class, 'location_camion_camions','location_id','camion_id')->withTimestamps()->withPivot('location_id');
    }

    protected $casts=[
        'camion'=>'array',
        'details'=>'array',
    ];




    public function pointages(){
        return $this->hasMany(PointageCamion::class,'location','id');
    }


}
