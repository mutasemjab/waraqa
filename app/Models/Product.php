<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;

class Product extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $appends = ['price_without_tax'];

    public function getPriceWithoutTaxAttribute()
    {
        $tax = $this->tax ?? 15;
        return $this->selling_price / (1 + ($tax / 100));
    }

     public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function provider()
    {
        return $this->belongsTo(Provider::class);
    }

    public function orderProducts()
    {
        return $this->hasMany(OrderProduct::class);
    }

   public function voucherProducts()
    {
        return $this->hasMany(VoucherProduct::class);
    }

    public function bookRequests()
    {
        return $this->hasMany(BookRequest::class);
    }

}
