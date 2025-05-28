<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PollingStation extends Model
{
    protected $fillable = [
        'name',
        'longitude',
        'latitude',  
        'region',
    ];


    public function pollingStationStaffs(){
        return $this->hasMany(PollingStationStaff::class);
    }

    public function constituency(){
        return $this->belongsTo(Constituency::class);
    }
}
