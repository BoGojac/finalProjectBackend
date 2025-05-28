<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Party extends Model
{
    protected $fillable = [
        'name',
        'abbrevation',
        'leader',
        'foundation_year',
        'participation_area',
        'image',
    ];



    public function candidates(){
        return $this->hasMany(Candidate::class);
    }
}
