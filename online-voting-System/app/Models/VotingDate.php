<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VotingDate extends Model
{
    protected $fillable = [
        'title',
        'voting_date'
    ];

    public function registrationTimeSpam(){
        return $this->hasMany(RegistrationTimeSpan::class);
    }

     public function user()
    {
        return $this->hasMany(User::class);
    }

     public function party()
    {
        return $this->hasMany(Party::class);
    }

    public function constituency(){
        return $this->hasMany(Constituency::class);
    }

     public function pollingStations()
    {
        return $this->hasMany(PollingStation::class);
    }

    public function regions()
    {
        return $this->hasMany(Region::class);
    }
}
