<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Voter extends Model
{
    protected $fillable=[
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
