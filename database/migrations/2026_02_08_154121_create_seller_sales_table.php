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
        Schema::create('seller_sales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->string('sale_number')->unique(); // رقم البيع مثل PO-1001
            $table->dateTime('sale_date'); // تاريخ البيع
            $table->decimal('total_amount', 12, 2)->default(0); // إجمالي المبلغ (شامل الضريبة)
            $table->decimal('total_tax', 10, 2)->default(0); // إجمالي الضريبة
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
        Schema::dropIfExists('seller_sales');
    }
};
