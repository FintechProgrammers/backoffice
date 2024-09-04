<?php

namespace App\Models;

use App\Traits\GeneratesUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommissionTransaction extends Model
{
    use HasFactory, GeneratesUuid;

    protected $guarded = [];

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id')->withTrashed();
    }

    function associate()
    {
        return $this->belongsTo(User::class, 'child_id', 'id')->withTrashed();
    }

    function sale()
    {
        return $this->belongsTo(Sale::class, 'sale_id', 'id');
    }
}
