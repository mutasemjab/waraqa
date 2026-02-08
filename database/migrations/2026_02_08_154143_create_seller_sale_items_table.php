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
        Schema::create('seller_sale_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seller_sale_id')->constrained('seller_sales')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->string('product_name'); // اسم المنتج (نسخة منه للأمان)
            $table->string('product_code')->nullable(); // كود المنتج
            $table->integer('quantity'); // الكمية
            $table->decimal('unit_price', 10, 2); // سعر الوحدة
            $table->decimal('tax_percentage', 5, 2)->default(0); // نسبة الضريبة
            $table->decimal('total_price_before_tax', 12, 2); // الإجمالي بدون ضريبة
            $table->decimal('total_tax', 10, 2)->default(0); // الضريبة الإجمالية
            $table->decimal('total_price_after_tax', 12, 2); // الإجمالي شامل الضريبة
            $table->text('notes')->nullable(); // ملاحظات
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
        Schema::dropIfExists('seller_sale_items');
    }
};
