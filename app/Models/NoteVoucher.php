<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NoteVoucher extends Model
{
    use HasFactory;

    protected $guarded=[];

    protected $casts = [
        'date_note_voucher' => 'date'
    ];

    public function fromWarehouse()
    {
        return $this->belongsTo(Warehouse::class, 'from_warehouse_id');
    }

    public function toWarehouse()
    {
        return $this->belongsTo(Warehouse::class, 'to_warehouse_id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function noteVoucherType()
    {
        return $this->belongsTo(NoteVoucherType::class);
    }

    public function voucherProducts()
    {
        return $this->hasMany(VoucherProduct::class);
    }

}
