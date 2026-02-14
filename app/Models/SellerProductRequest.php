<?php

namespace App\Models;

use App\Enums\SellerProductRequestStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SellerProductRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'status',
        'note',
        'approved_by',
        'approved_at',
        'rejection_reason',
        'order_id',
    ];

    protected $casts = [
        'status' => SellerProductRequestStatus::class,
        'approved_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(SellerProductRequestItem::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function scopePending($query)
    {
        return $query->where('status', SellerProductRequestStatus::PENDING->value);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', SellerProductRequestStatus::APPROVED->value);
    }

    public function scopeRejected($query)
    {
        return $query->where('status', SellerProductRequestStatus::REJECTED->value);
    }
}
