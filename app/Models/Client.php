<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $fillable = [
        "designation",
        "email",
        "phone",
        "domaine",
        "logo",
        "observation",
    ];

    public function locations()
    {
        return $this->hasMany(LocationCamion::class, 'client','designation');
    }
}
