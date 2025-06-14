<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Voter extends Model
{
    protected $fillable=[
        'user_id',
        'polling_station_id',
        'first_name',
        'middle_name',
        'last_name',
        'gender',
        'registration_date',
        'birth_date',
        'disability',
        'duration_of_residence',
        'home_number',
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }
}
