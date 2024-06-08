<?php

namespace App\Models;

use App\Traits\GeneratesUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory, GeneratesUuid;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = [];

    /**
     * Define the route model binding key for a given model.
     */
    public function getRouteKeyName()
    {
        return 'uuid';
    }

    function serviceProduct()
    {
        return $this->hasMany(ServiceProduct::class);
    }

    public function getImageAttribute(): string
    {
        return !empty($this->image_url) ? $this->image_url : url('/') . '/assets/images/default.jpg';
    }
}
