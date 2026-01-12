<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_number',
        'provider_id',
        'warehouse_id',
        'book_request_response_id',
        'total_amount',
        'total_tax',
        'status',
        'expected_delivery_date',
        'received_date',
        'notes',
    ];

    protected $dates = [
        'expected_delivery_date',
        'received_date',
    ];

    public function provider()
    {
        return $this->belongsTo(Provider::class);
    }

    public function bookRequestResponse()
    {
        return $this->belongsTo(BookRequestResponse::class);
    }

    public function items()
    {
        return $this->hasMany(PurchaseItem::class);
    }

    public function noteVoucher()
    {
        return $this->hasOne(NoteVoucher::class, 'purchase_id');
    }
}
