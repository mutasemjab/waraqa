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
        Schema::create('seller_product_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->tinyInteger('status')->default(1); // 1: pending, 2: approved, 3: rejected
            $table->text('note')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->foreignId('order_id')->nullable()->constrained('orders')->onDelete('set null');
            $table->timestamps();

            $table->index('user_id');
            $table->index('status');
            $table->index('created_at');
        });

        Schema::create('seller_product_request_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seller_product_request_id')->constrained('seller_product_requests')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->integer('requested_quantity');
            $table->integer('approved_quantity')->nullable();
            $table->decimal('approved_price', 10, 2)->nullable();
            $table->decimal('approved_tax_percentage', 5, 2)->nullable();
            $table->timestamps();

            $table->index('seller_product_request_id');
            $table->index('product_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('seller_product_request_items');
        Schema::dropIfExists('seller_product_requests');
    }
};
