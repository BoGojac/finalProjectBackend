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
        'headquarters',
        'participation_area',
        'region_id',
        'status',
        'image',
        'original_image_name',
    ];



    public function candidates(){
        return $this->hasMany(Candidate::class);
    }

    public function region(){
        return $this->belongsTo(Region::class);
    }

}
