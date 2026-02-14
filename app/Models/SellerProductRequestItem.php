<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SellerProductRequestItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'seller_product_request_id',
        'product_id',
        'requested_quantity',
        'approved_quantity',
        'approved_price',
        'approved_tax_percentage',
    ];

    protected $casts = [
        'approved_price' => 'decimal:2',
        'approved_tax_percentage' => 'decimal:2',
    ];

    public function sellerProductRequest()
    {
        return $this->belongsTo(SellerProductRequest::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
