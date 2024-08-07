<?php

namespace App\Models;

use App\Traits\GeneratesUuid;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserSubscription extends Model
{
    use HasFactory, GeneratesUuid;

    protected $guarded = [];

    function user()
    {
        return $this->belongsTo(User::class, 'user_id')->withTrashed();
    }

    function service()
    {
        return $this->belongsTo(Service::class, 'service_id')->withTrashed();
    }

    // Alternatively, if using expiration dates:
    public function scopeActive($query)
    {
        return $query->where('is_active', true)->where('end_date', '>', Carbon::now());
    }

    public function scopeExpiringInOneWeek($query)
    {
        $now = Carbon::now();
        $oneWeekFromNow = $now->addWeek();
        return $query->whereBetween('end_date', [$now, $oneWeekFromNow]);
    }

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];
}
