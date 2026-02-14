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
        Schema::create('book_request_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('book_request_item_id')->constrained('book_request_items')->onDelete('cascade');
            $table->foreignId('provider_id')->constrained('providers')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->integer('available_quantity');
            $table->decimal('price', 12, 3)->nullable();
            $table->decimal('tax_percentage', 5, 2)->default(0);
            $table->string('status')->default('pending'); // pending, approved, rejected
            $table->text('note')->nullable();
            $table->date('expected_delivery_date')->nullable();
            $table->timestamps();

            $table->index('book_request_item_id');
            $table->index('provider_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('book_request_responses');
    }
};
