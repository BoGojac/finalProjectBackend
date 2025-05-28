<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BoardManager extends Model
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
}
