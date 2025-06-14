<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Constituency extends Model
{
    protected $fillable = [
        'name',
        'longitude',
        'latitude',
        'region_id',
        'status',
    ];

    public function pollingStations()
    {
        return $this->hasMany(PollingStation::class);
    }

    public function constituencyStaffs()
    {
        return $this->hasMany(ConstituencyStaff::class);
    }

    public function candidates()
    {
        return $this->hasMany(Candidate::class);
    }

    public function region()
    {
        return $this->belongsTo(Region::class);
    }
}
