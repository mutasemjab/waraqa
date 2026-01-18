<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model
{
    use HasFactory;
    protected $guarded=[];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function outgoingNoteVouchers()
    {
        return $this->hasMany(NoteVoucher::class, 'from_warehouse_id');
    }

    public function incomingNoteVouchers()
    {
        return $this->hasMany(NoteVoucher::class, 'to_warehouse_id');
    }
    public function getTotalQuantityAttribute()
    {
        // 1. Calculate Total Input:
        //    - Entry Vouchers (Type 1) to this warehouse
        //    - Transfer Vouchers (Type 3) to this warehouse
        $input = \Illuminate\Support\Facades\DB::table('voucher_products')
            ->join('note_vouchers', 'voucher_products.note_voucher_id', '=', 'note_vouchers.id')
            ->join('note_voucher_types', 'note_vouchers.note_voucher_type_id', '=', 'note_voucher_types.id')
            ->where(function($query) {
                // Type 1 (Entry) to this warehouse
                $query->where(function($q) {
                    $q->where('note_voucher_types.in_out_type', 1)
                      ->where('note_vouchers.to_warehouse_id', $this->id);
                })
                // OR Type 3 (Transfer) TO this warehouse
                ->orWhere(function($q) {
                    $q->where('note_voucher_types.in_out_type', 3)
                      ->where('note_vouchers.to_warehouse_id', $this->id);
                });
            })
            ->sum('voucher_products.quantity');

        // 2. Calculate Total Output:
        //    - Exit Vouchers (Type 2) from this warehouse
        //    - Transfer Vouchers (Type 3) from this warehouse
        $output = \Illuminate\Support\Facades\DB::table('voucher_products')
            ->join('note_vouchers', 'voucher_products.note_voucher_id', '=', 'note_vouchers.id')
            ->join('note_voucher_types', 'note_vouchers.note_voucher_type_id', '=', 'note_voucher_types.id')
            ->whereIn('note_voucher_types.in_out_type', [2, 3]) // Exit or Transfer
            ->where('note_vouchers.from_warehouse_id', $this->id)
            ->sum('voucher_products.quantity');

        return max(0, $input - $output);
    }

    public function movements()
    {
        return NoteVoucher::where('from_warehouse_id', $this->id)
            ->orWhere('to_warehouse_id', $this->id)
            ->with(['noteVoucherType', 'fromWarehouse', 'toWarehouse', 'voucherProducts.product'])
            ->orderBy('date_note_voucher', 'desc')
            ->orderBy('id', 'desc');
    }
}
