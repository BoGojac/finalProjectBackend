<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VoteCount extends Model
{
    protected $fillable = [
        'voting_date_id',
        'candidate_id',
        'voter_id',
    ];

    // Relationships

    public function votingDate()
    {
        return $this->belongsTo(VotingDate::class);
    }

    public function candidate()
    {
        return $this->belongsTo(Candidate::class);
    }

    public function voter()
    {
        return $this->belongsTo(Voter::class);
    }
}

