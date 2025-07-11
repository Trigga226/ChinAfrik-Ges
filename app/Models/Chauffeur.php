<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Chauffeur extends Model
{
    protected $fillable = [
        "nom",
        "prenom",
        "cni",
        "phone",
        "scann_doc",
    ];

    protected $casts=[
        "scann_doc"=>"array",
    ];


    public function pointages(){
        return $this->hasMany(PointageCamion::class,'chauffeur','id');
    }

}
