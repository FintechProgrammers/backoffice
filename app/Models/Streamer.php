<?php

namespace App\Models;

use App\Traits\GeneratesUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Streamer extends Authenticatable
{
    use HasFactory, GeneratesUuid, HasApiTokens, Notifiable;

    protected $guarded = [];

    public function getRouteKeyName()
    {
        return 'uuid';
    }

    public function getProfilePictureAttribute(): string
    {
        return !empty($this->profile_image) ? $this->profile_image : url('/') . '/assets/images/default-dp.png';
    }

    /**
     * Get the full name of the user.
     */
    public function getFullNameAttribute(): string
    {
        return !empty($this->first_name) ? \Illuminate\Support\Str::title($this->first_name . ' ' . $this->last_name) : 'Unavailable';
    }

    function chatGroup()
    {
        return $this->hasOne(ChatGroup::class, 'streamer_id', 'id');
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];


    /* Get the attributes that should be cast.
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
}
