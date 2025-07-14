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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->text('number')->nullable();
            $table->tinyInteger('status')->default(1);  // 1 Done // 2 Canceled // 6 Refund
            $table->double('total_taxes');
            $table->double('total_prices');
            $table->tinyInteger('payment_status')->default(2); // 1 Paid   // 2 Unpaid
            $table->tinyInteger('order_type')->default(1);  // 1 Sell   // 2 Refund
            $table->dateTime('date');
            $table->text('note')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->double('paid_amount')->default(0);
            $table->double('remaining_amount')->default(0);
    
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
        Schema::dropIfExists('orders');
    }
};
