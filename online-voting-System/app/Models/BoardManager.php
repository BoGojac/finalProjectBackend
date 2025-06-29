<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BoardManager extends Model
{
    protected $fillable=[
        'user_id',
        'first_name',
        'middle_name',
        'last_name',
        'gender',
    ];


    /**
     * to set eloquent relation
     */

    public function user(){
        return $this->belongsTo(User::class);
    }
}
