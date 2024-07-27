<?php

namespace App\Models;

use App\Observers\SaleObserver;
use App\Traits\GeneratesUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory, GeneratesUuid;

    protected $guarded = [];

    function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    function parent()
    {
        return $this->belongsTo(User::class, 'parent_id', 'id');
    }

    function service()
    {
        return $this->belongsTo(Service::class, 'service_id', 'id');
    }
}