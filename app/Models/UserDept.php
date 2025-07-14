<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserDept extends Model
{
    use HasFactory;

    protected $guarded=[];
    
     protected $casts = [
        'total_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'remaining_amount' => 'decimal:2',
    ];

    /**
     * Get the user that owns the debt
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the order associated with the debt
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get status label
     */
    public function getStatusLabelAttribute()
    {
        return $this->status == 1 ? __('messages.active') : __('messages.paid');
    }

    /**
     * Get status badge class
     */
    public function getStatusBadgeAttribute()
    {
        return $this->status == 1 ? 'badge-danger' : 'badge-success';
    }

    /**
     * Scope for active debts
     */
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    /**
     * Scope for paid debts
     */
    public function scopePaid($query)
    {
        return $query->where('status', 2);
    }
}
