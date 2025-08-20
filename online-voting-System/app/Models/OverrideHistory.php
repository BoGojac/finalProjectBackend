<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OverrideHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'voting_date_id',
        'user_id',
        'override_level',
        'constituency_id',
        'polling_station_id',
        'override_date',
        'rollback_status',
        'rollback_user_id',
        'rollback_date',
        'substitution_date',
    ];

    public function votingDate()
    {
        return $this->belongsTo(VotingDate::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function rollbackUser()
    {
        return $this->belongsTo(User::class, 'rollback_user_id');
    }

    public function constituency()
    {
        return $this->belongsTo(Constituency::class);
    }

    public function pollingStation()
    {
        return $this->belongsTo(PollingStation::class);
    }
}
