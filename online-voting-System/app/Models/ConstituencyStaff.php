<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConstituencyStaff extends Model
{
    protected $fillable=[
        'first_name',
        'middle_name',
        'last_name',
        'gender',
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function constituency(){
        return $this->belongsTo(Constituency::class);
    }
}
