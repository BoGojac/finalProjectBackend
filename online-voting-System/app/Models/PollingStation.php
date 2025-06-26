<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PollingStation extends Model
{
    protected $fillable = [
        'constituency_id',
        'voting_date_id',
        'name',
        'longitude',
        'latitude',
        'status',
    ];

    // A polling station has many polling station staff
    public function pollingStationStaffs()
    {
        return $this->hasMany(PollingStationStaff::class);
    }

    // A polling station belongs to one constituency
    public function constituency()
    {
        return $this->belongsTo(Constituency::class,);
    }

    // Access region through the constituency
    public function region()
    {
        return $this->constituency ? $this->constituency->region : null;
    }
    public function voting_date(){
        return $this->belongsTo(VotingDate::class);
    }
}
