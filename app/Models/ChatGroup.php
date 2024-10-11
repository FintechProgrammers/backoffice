<?php

namespace App\Models;

use App\Traits\GeneratesUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatGroup extends Model
{
    use HasFactory, GeneratesUuid;

    protected $guarded = [];

    function streamer()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
