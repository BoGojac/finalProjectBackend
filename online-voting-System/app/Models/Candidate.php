<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Candidate extends Model
{
    protected $fillable=[
        'user_id',
        'party_id',
        'constituency_id',
        'first_name',
        'middle_name',
        'last_name',
        'gender',
        'registration_date',
        'birth_date',
        'disability',
        'duration_of_residence',
        'home_number',
        'image',
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function party(){
        return $this->belongsTo(Party::class);
    }

    public function constituency(){
        return $this->belongsTo(Constituency::class);
    }

    // Access region through the party
    public function region()
    {
        return $this->party ? $this->party->region : null;
    }
}
