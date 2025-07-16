<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SuiviCamion extends Model
{
    protected $fillable = [
        "date",
        "camion",
        "chauffeur",
        "type_entretient",
        "piece_change",
        "decription_panne",
        "kilometrage",
        "duree_immobilisation",
        "atelier",
        "observation",
        "document",
    ];

    public function camions():BelongsTo{
        return $this->belongsTo(Camion::class,'camion','immatriculation');
    }

    public function chauffeurs():BelongsTo{
        return $this->belongsTo(Chauffeur::class,'chauffeur','id');
    }

}
