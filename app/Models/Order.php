<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $guarded = [];
    
    protected $casts = [
        'date'=>'date',
        'order_date'=>'date',
    ];


   public function orderProducts()
    {
        return $this->hasMany(OrderProduct::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function userDepts()
    {
        return $this->hasMany(UserDept::class);
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function salesReturns()
    {
        return $this->hasMany(SalesReturn::class);
    }

    public function sellerProductRequest()
    {
        return $this->hasOne(SellerProductRequest::class);
    }
}
