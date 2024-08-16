<?php

namespace App\Models;

use App\Traits\GeneratesUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Service extends Model
{
    use HasFactory, GeneratesUuid, SoftDeletes;

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

    /**
     * Get the balance attribute.
     *
     * @param  int  $value
     * @return float
     */
    public function getPriceAttribute($value)
    {
        return round($value, 2);
    }

    public function getBvAmountAttribute($value)
    {
        return round($value, 2);
    }


    function serviceProduct()
    {
        return $this->hasMany(ServiceProduct::class);
    }

    public function getImageAttribute(): string
    {
        return !empty($this->image_url) ? $this->image_url : asset('assets/images/default.jpg');
    }

    public function getBannerUrlAttribute(): string
    {
        return !empty($this->banner) ? $this->banner : asset('assets/images/default.jpg');
    }

    public function getProductImageUrlAttribute(): string
    {
        return !empty($this->product_image) ? $this->product_image : asset('assets/images/default.jpg');
    }

    function sales()
    {
        return $this->hasMany(Sale::class, 'service_id', 'id');
    }

    function getTotalSalesAttribute()
    {
        return $this->sales()->sum('amount');
    }
}
