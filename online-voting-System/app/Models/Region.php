<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Region extends Model
{
     protected $fillable=[
        'name',
        'abbreviation',
        'voting_date_id',
     ];

     /**
     * to set eloquent relation
     */

     public function constituencies(){
        return $this->hasMany(Constituency::class);
    }

    public function parties(){
        return $this->hasMany(Party::class);
    }

    public function voting_date(){
        return $this->belongsTo(VotingDate::class);
    }
}
