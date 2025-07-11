<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DepenseCamion extends Model
{
    protected $fillable=[
        "camion",
        "date",
        "motif",
        "description",
        "piece",
        "montant",
    ];

    protected $casts=[
        "piece"=> "array",
    ];


    public function camions(){
        return $this->belongsTo(Camion::class,'camion','designation');
    }
}
