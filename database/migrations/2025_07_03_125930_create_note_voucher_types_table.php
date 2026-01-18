<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
   public function up()
    {
        Schema::create('note_voucher_types', function (Blueprint $table) {
            $table->id();
            $table->integer('number');
            $table->string('name');
            $table->string('name_en')->nullable();
            $table->tinyInteger('in_out_type'); 
            $table->tinyInteger('have_price'); //1 yes // 2 no
            $table->text('header')->nullable();
            $table->text('footer')->nullable();
            $table->timestamps();
        });
        DB::table('note_voucher_types')->insert([
            [
                'number' => 1,
                'name' => "سند ادخال",
                'name_en' => "Receipt Note Voucher",
                'in_out_type' => 1,
                'have_price' => 2,
                'header' => ' <div class="row">
            <div class="col-5 center">
                <div class="bold" style="font-size:30px">اسم الشركة</div>
            </div>
            <div class="col-2 center">
                <img width="180px" height="100px" src="https://softya.com/demo/public/storage/logo.png" />
            </div>
            <div class="col-5 center">
                <div class="bold" style="font-size:30px">Company name</div>
            </div>
        </div> '
            ],
            [
                'number' => 2,
                'name' => "سند اخراج",
                'name_en' => "Out Note Voucher",
                'in_out_type' => 2,
                'have_price' => 2,
                'header' => ' <div class="row">
                <div class="col-5 center">
                    <div class="bold" style="font-size:30px">اسم الشركة</div>
                </div>
                <div class="col-2 center">
                    <img width="180px" height="100px" src="https://softya.com/demo/public/storage/logo.png" />
                </div>
                <div class="col-5 center">
                    <div class="bold" style="font-size:30px">Company name</div>
                </div>
            </div> '
            ],
            [
                'number' => 3,
                'name' => "سند نقل",
                'name_en' => "Transfer Note Voucher",
                'in_out_type' => 3,
                'have_price' => 2,
                'header' => ' <div class="row">
                <div class="col-5 center">
                    <div class="bold" style="font-size:30px">اسم الشركة</div>
                </div>
                <div class="col-2 center">
                    <img width="180px" height="100px" src="https://softya.com/demo/public/storage/logo.png" />
                </div>
                <div class="col-5 center">
                    <div class="bold" style="font-size:30px">Company name</div>
                </div>
            </div> '
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('note_voucher_types');
    }
};
