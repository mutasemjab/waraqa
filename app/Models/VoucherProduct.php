<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VoucherProduct extends Model
{
    use HasFactory;

    protected $guarded=[];

     public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function noteVoucher()
    {
        return $this->belongsTo(NoteVoucher::class);
    }
}
