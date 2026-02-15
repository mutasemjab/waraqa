<?php

namespace App\Models;

use App\Enums\SellerSaleStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SellerSale extends Model
{
    use HasFactory;

    protected $guarded = [];

    /**
     * Cast attributes to their native types
     */
    protected $casts = [
        'status' => SellerSaleStatus::class,
        'approved_at' => 'datetime',
    ];

    /**
     * Get the user who created this sale
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the items in this sale
     */
    public function items()
    {
        return $this->hasMany(SellerSaleItem::class);
    }

    /**
     * Get the admin who approved/rejected this sale
     */
    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Get the product (if needed for relationships)
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Scope to get only pending sales
     */
    public function scopePending($query)
    {
        return $query->where('status', SellerSaleStatus::PENDING->value);
    }

    /**
     * Scope to get only approved sales
     */
    public function scopeApproved($query)
    {
        return $query->where('status', SellerSaleStatus::APPROVED->value);
    }

    /**
     * Scope to get only rejected sales
     */
    public function scopeRejected($query)
    {
        return $query->where('status', SellerSaleStatus::REJECTED->value);
    }
}
