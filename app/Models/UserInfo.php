<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserInfo extends Model
{
    use HasFactory;

    protected $guarded = [];

    function country()
    {
        return $this->belongsTo(Country::class, 'country_code', 'iso2');
    }
}
