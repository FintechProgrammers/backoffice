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

    /**
     * Define the route model binding key for a given model.
     */
    public function getRouteKeyName()
    {
        return 'uuid';
    }

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
        return $query->where('end_date', '>', Carbon::now());
    }

    public function scopeExpiringInOneWeek($query)
    {
        $now = Carbon::now();
        $oneWeekFromNow = $now->addWeek();
        return $query->whereBetween('end_date', [$now, $oneWeekFromNow]);
    }

    public function progressPercentage()
    {
        $start = $this->start_date;
        $end = $this->end_date;
        $now = now();

        if ($end < $start || $now > $end) {
            return 100;
        } elseif ($now < $start) {
            return 0;
        } else {
            $totalDuration = $end->diffInSeconds($start);
            $elapsedDuration = $now->diffInSeconds($start);
            return ($elapsedDuration / $totalDuration) * 100;
        }
    }

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];
}
