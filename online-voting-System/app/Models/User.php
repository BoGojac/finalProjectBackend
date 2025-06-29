<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'voting_date_id',
        'username',
        'email',
        'password',
        'role',
        'phone_number',
        'status',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * to set eloquent relation
     */

    public function admin(){
        return $this->hasOne(Admin::class);
    }

    public function board_manager(){
        return $this->hasOne(BoardManager::class);
    }

    public function constituency_staffs(){
        return $this->hasOne(ConstituencyStaff::class);
    }

    public function polling_station_staff(){
        return $this->hasOne(PollingStationStaff::class);
    }

    public function candidates(){
        return $this->hasOne(Candidate::class);
    }

    public function voters(){
        return $this->hasOne(Voter::class);
    }

    public function voting_date(){
        return $this->belongsTo(VotingDate::class);
    }

    public function constituency()
    {
        return $this->belongsTo(Constituency::class);
    }
}
