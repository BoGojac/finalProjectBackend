<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PollingStation extends Model
{
    protected $fillable = [
        'constituencies_id',
        'name',
        'longitude',
        'latitude',
         // 'status',
    ];

    // A polling station has many polling station staff
    public function pollingStationStaffs()
    {
        return $this->hasMany(PollingStationStaff::class);
    }

    // A polling station belongs to one constituency
    public function constituency()
    {
        return $this->belongsTo(Constituency::class, 'constituencies_id');
    }

    // Access region through the constituency
    public function region()
    {
        return $this->constituency ? $this->constituency->region : null;
    }
}
