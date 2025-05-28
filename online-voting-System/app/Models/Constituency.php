<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Constituency extends Model
{
    
    protected $fillable = [
        'name',
        'longitude',
        'latitude',
        'region',
    ];



    public function pollingStations(){
        return $this->hasMany(PollingStation::class);
    }

    public function constituencyStaffs(){
        return $this->hasMany(ConstituencyStaff::class);
    }

}
