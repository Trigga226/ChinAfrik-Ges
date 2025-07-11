<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CategorieMachine extends Model
{
    protected $fillable = [
        "designation",
        "description"
    ];

    public function machines(){
        return $this->hasMany(Machine::class, 'categorie','designation');
    }
}
