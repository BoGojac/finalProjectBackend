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
}
