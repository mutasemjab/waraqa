<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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
        Schema::create('countries', function (Blueprint $table) {
            $table->id();
            $table->string('name_en')->unique();
            $table->string('name_ar')->unique();
            $table->timestamps();
        });

      DB::table('countries')->insert([
            ['name_en' => "Amman",   'name_ar' => "عمان"],
            ['name_en' => "Zarqa",   'name_ar' => "الزرقاء"],
            ['name_en' => "Jarash",  'name_ar' => "جرش"],
            ['name_en' => "Karak",   'name_ar' => "الكرك"],
            ['name_en' => "Salt",    'name_ar' => "السلط"],
            ['name_en' => "Aqaba",   'name_ar' => "العقبة"],
            ['name_en' => "Irbid",   'name_ar' => "إربد"],
            ['name_en' => "Ramtha",  'name_ar' => "الرمثا"],
            ['name_en' => "Tafela",  'name_ar' => "الطفيلة"],
            ['name_en' => "Maan",    'name_ar' => "معان"],
            ['name_en' => "Mafraq",  'name_ar' => "المفرق"],
        ]);

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('countries');
    }
};
