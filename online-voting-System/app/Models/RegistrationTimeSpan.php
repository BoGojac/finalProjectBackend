<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RegistrationTimeSpan extends Model
{
    protected $fillable = [
        'voting_date_id',
        'beginning_date',
        'ending_date'
    ];

    public function votingDate(){
        return $this->belongsTo(VotingDate::class);
    }


}
