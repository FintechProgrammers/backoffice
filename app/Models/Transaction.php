<?php

namespace App\Models;

use App\Traits\GeneratesUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory, GeneratesUuid;

    protected $guarded = [];

    function user()
    {
        return $this->belongsTo(User::class, 'user_id')->withTrashed();
    }

    function associatedUser()
    {
        return $this->belongsTo(User::class, 'associated_user_id', 'id')->withTrashed();
    }
}
