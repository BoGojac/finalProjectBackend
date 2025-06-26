<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Constituency extends Model
{



    protected $fillable = [
        'voting_date_id',
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

    public function constituencyStaff()
    {
        return $this->hasMany(ConstituencyStaff::class);
    }

    public function user(){
        return $this->hasMany(User::class);
    }

    public function candidates()
    {
        return $this->hasMany(Candidate::class);
    }

    public function region()
    {
        return $this->belongsTo(Region::class);
    }
    public function voting_date(){
        return $this->belongsTo(VotingDate::class);
    }

}
