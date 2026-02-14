<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookRequestResponse extends Model
{
    use HasFactory;

    protected $fillable = ['book_request_item_id', 'provider_id', 'user_id', 'available_quantity', 'price', 'tax_percentage', 'status', 'note', 'expected_delivery_date'];

    public function bookRequestItem()
    {
        return $this->belongsTo(BookRequestItem::class);
    }

    public function provider()
    {
        return $this->belongsTo(Provider::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
