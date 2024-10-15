<?php

namespace App\Models;

use App\Traits\GeneratesUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Signal extends Model
{
    use HasFactory, GeneratesUuid;

    protected $guarded = [];

    public function asset()
    {
        return $this->belongsTo(Asset::class, 'asset_type', 'id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }

    function streamer()
    {
        return $this->belongsTo(Streamer::class, 'streamer_id', 'id');
    }
}
