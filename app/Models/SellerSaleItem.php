<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SellerSaleItem extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function sellerSale()
    {
        return $this->belongsTo(SellerSale::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
