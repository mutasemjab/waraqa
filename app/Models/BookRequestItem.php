<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookRequestItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'book_request_id',
        'product_id',
        'requested_quantity',
    ];

    public function bookRequest()
    {
        return $this->belongsTo(BookRequest::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function responses()
    {
        return $this->hasMany(BookRequestResponse::class);
    }
}
