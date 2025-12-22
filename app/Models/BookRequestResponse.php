<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookRequestResponse extends Model
{
    use HasFactory;

    protected $fillable = ['book_request_id', 'provider_id', 'available_quantity', 'status', 'note'];

    public function bookRequest()
    {
        return $this->belongsTo(BookRequest::class);
    }

    public function provider()
    {
        return $this->belongsTo(Provider::class);
    }
}
