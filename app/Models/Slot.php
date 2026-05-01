<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Slot extends Model
{
      protected $fillable = [
        'vendor_id',
        'service',
        'date',
        'time',
        'is_booked'
    ];
}
