<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{

    protected $table = 'my_events';

    protected $fillable = [
        "name",
        "starts_at",
        "ends_at",
        "details",
    ];
}
