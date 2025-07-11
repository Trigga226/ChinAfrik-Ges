<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DepenseMachine extends Model
{
    protected $fillable=[
        "machine",
        "date",
        "motif",
        "description",
        "piece",
        "montant",
    ];

    protected $casts=[
        "piece"=> "array",
    ];


    public function machines(){
        return $this->belongsTo(Machine::class,'machine','designation');
    }
}
