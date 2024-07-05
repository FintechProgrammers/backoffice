<?php

namespace App\Models;

use App\Traits\GeneratesUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserKyc extends Model
{
    use HasFactory, GeneratesUuid, SoftDeletes;

    protected $guarded = [];

    const SERVICE = [
        'Face' => 'Face',
        'Address' => 'Address',
        'Documentation' => 'Documentation',
    ];

    const STATUS = [
        'pending' => 'pending',
        'verified' => 'verified',
        'declined' => 'declined',
    ];

    function user()
    {
        return $this->belongsTo(User::class);
    }
}
