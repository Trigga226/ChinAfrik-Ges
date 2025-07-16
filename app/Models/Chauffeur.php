<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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


    public function suivisCam():HasMany{
        return $this->hasMany(SuiviCamion::class,'chauffeur','id');
    }

    public function suiviMac():HasMany{
        return $this->hasMany(SuiviMachine::class,'chauffeur','id');
    }
}
