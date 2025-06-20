<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Region extends Model
{
     protected $fillable=[
        'name',
        'abbreviation'
     ];

     public function constituencies(){
        return $this->hasMany(Constituency::class);
    }

    public function parties(){
        return $this->hasMany(Party::class);
    }
}
