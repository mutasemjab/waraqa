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

    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }

    public function outgoingNoteVouchers()
    {
        return $this->hasMany(NoteVoucher::class, 'from_warehouse_id');
    }

    public function incomingNoteVouchers()
    {
        return $this->hasMany(NoteVoucher::class, 'to_warehouse_id');
    }
}
