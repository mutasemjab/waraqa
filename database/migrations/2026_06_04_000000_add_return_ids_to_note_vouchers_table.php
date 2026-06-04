<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('note_vouchers', function (Blueprint $table) {
            $table->unsignedBigInteger('sales_return_id')->nullable()->after('provider_id');
            $table->unsignedBigInteger('purchase_return_id')->nullable()->after('sales_return_id');

            $table->foreign('sales_return_id')
                ->references('id')->on('sales_returns')
                ->nullOnDelete();

            $table->foreign('purchase_return_id')
                ->references('id')->on('purchase_returns')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('note_vouchers', function (Blueprint $table) {
            $table->dropForeign(['sales_return_id']);
            $table->dropForeign(['purchase_return_id']);
            $table->dropColumn(['sales_return_id', 'purchase_return_id']);
        });
    }
};
