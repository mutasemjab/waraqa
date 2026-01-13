<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       Schema::create('note_vouchers', function (Blueprint $table) {
            $table->id();
            $table->integer('number');
            $table->date('date_note_voucher');
            $table->text('note')->nullable();
            $table->unsignedBigInteger('from_warehouse_id')->nullable();
            $table->unsignedBigInteger('to_warehouse_id')->nullable();
            $table->foreignId('provider_id')->nullable()->constrained('providers')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->unsignedBigInteger('event_id')->nullable();
            $table->unsignedBigInteger('order_id')->nullable();
            $table->unsignedBigInteger('note_voucher_type_id');
            $table->foreign('from_warehouse_id')->references('id')->on('warehouses')->onDelete('cascade');
            $table->foreign('to_warehouse_id')->references('id')->on('warehouses')->onDelete('cascade');
            $table->foreign('event_id')->references('id')->on('events')->onDelete('cascade');
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            $table->foreign('note_voucher_type_id')->references('id')->on('note_voucher_types')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('note_vouchers');
    }
};