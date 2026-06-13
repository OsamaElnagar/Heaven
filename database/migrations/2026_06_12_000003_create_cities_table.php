<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cities', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('name_ar');
            $table->string('country')->nullable();
            $table->timestamps();
        });

        DB::table('cities')->insert([
            [
                'name' => 'Makkah',
                'name_ar' => 'مكة المكرمة',
                'country' => 'Saudi Arabia',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Madinah',
                'name_ar' => 'المدينة المنورة',
                'country' => 'Saudi Arabia',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('cities');
    }
};
