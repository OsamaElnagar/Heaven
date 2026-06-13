<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('package_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('name_ar');
            $table->string('slug')->unique();
            $table->string('color')->default('gray');
            $table->string('icon')->nullable();
            $table->boolean('is_religious')->default(false);
            $table->unsignedSmallInteger('duration_nights_min')->nullable();
            $table->unsignedSmallInteger('duration_nights_max')->nullable();
            $table->timestamps();
        });

        DB::table('package_types')->insert([
            [
                'name' => 'Hajj',
                'name_ar' => 'حج',
                'slug' => 'hajj',
                'color' => 'warning',
                'icon' => 'heroicon-o-star',
                'is_religious' => true,
                'duration_nights_min' => 14,
                'duration_nights_max' => 30,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Umrah',
                'name_ar' => 'عمرة',
                'slug' => 'umrah',
                'color' => 'success',
                'icon' => 'heroicon-o-moon',
                'is_religious' => true,
                'duration_nights_min' => 7,
                'duration_nights_max' => 21,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('package_types');
    }
};
