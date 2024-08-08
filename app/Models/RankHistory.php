<?php

namespace App\Models;

use App\Traits\GeneratesUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RankHistory extends Model
{
    use HasFactory, GeneratesUuid;

    protected $guarded = [];

    function rank()
    {
        return $this->belongsTo(Rank::class, 'rank_id', 'id');
    }
}
