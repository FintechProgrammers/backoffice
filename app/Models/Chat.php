<?php

namespace App\Models;

use App\Traits\GeneratesUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    use HasFactory, GeneratesUuid;


    protected $guarded = [];

    /**
     * Define the route model binding key for a given model.
     */
    public function getRouteKeyName()
    {
        return 'uuid';
    }

    function chatGroup()
    {
        return $this->belongsTo(ChatGroup::class);
    }

    function user()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    function streamer()
    {
        return $this->belongsTo(Streamer::class, 'sender_id');
    }
}
