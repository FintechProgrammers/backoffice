<?php

namespace App\Models;

use App\Traits\GeneratesUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceStreamer extends Model
{
    use HasFactory;

    protected $guarded = [];

    function streamer()
    {
        return $this->belongsTo(Streamer::class, 'streamer_id');
    }

    function service()
    {
        return $this->belongsTo(Service::class, 'service_id');
    }
}
