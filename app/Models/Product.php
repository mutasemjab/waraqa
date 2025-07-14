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

}
