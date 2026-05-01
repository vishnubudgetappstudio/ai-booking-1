<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $fillable = [
        'vendor_id',
        'name',
        'service',
        'date',
        'time',
        'status'
    ];
}
