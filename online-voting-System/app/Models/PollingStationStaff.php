<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PollingStationStaff extends Model
{
    protected $fillable=[
        'user_id',
        'polling_station_id',
        'first_name',
        'middle_name',
        'last_name',
        'gender',
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }
}
