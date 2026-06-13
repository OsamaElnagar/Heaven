<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hotels', function (Blueprint $table) {
            $table->foreignId('city_id')->nullable()->after('city');
        });

        $cities = DB::table('cities')->pluck('id', 'name');

        $cityMap = [
            'makkah' => $cities['Makkah'] ?? null,
            'madinah' => $cities['Madinah'] ?? null,
        ];

        foreach ($cityMap as $oldValue => $id) {
            if ($id) {
                DB::table('hotels')->where('city', $oldValue)->update(['city_id' => $id]);
            }
        }

        Schema::table('hotels', function (Blueprint $table) {
            $table->dropColumn('city');
            $table->foreign('city_id')->references('id')->on('cities')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('hotels', function (Blueprint $table) {
            $table->string('city')->nullable()->after('city_id');
        });

        $cities = DB::table('cities')->pluck('name', 'id');

        $reverseMap = [
            'Makkah' => 'makkah',
            'Madinah' => 'madinah',
        ];

        foreach ($cities as $id => $name) {
            $oldValue = $reverseMap[$name] ?? strtolower($name);
            DB::table('hotels')->where('city_id', $id)->update(['city' => $oldValue]);
        }

        Schema::table('hotels', function (Blueprint $table) {
            $table->dropForeign(['city_id']);
            $table->dropColumn('city_id');
        });
    }
};
