<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('packages', function (Blueprint $table) {
            $table->foreignId('type_id')->nullable()->after('type');
        });

        $types = DB::table('package_types')->pluck('id', 'slug');

        foreach ($types as $slug => $id) {
            DB::table('packages')->where('type', $slug)->update(['type_id' => $id]);
        }

        Schema::table('packages', function (Blueprint $table) {
            $table->dropColumn('type');
            $table->foreign('type_id')->references('id')->on('package_types')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('packages', function (Blueprint $table) {
            $table->string('type')->nullable()->after('type_id');
        });

        $types = DB::table('package_types')->pluck('slug', 'id');

        foreach ($types as $id => $slug) {
            DB::table('packages')->where('type_id', $id)->update(['type' => $slug]);
        }

        Schema::table('packages', function (Blueprint $table) {
            $table->dropForeign(['type_id']);
            $table->dropColumn('type_id');
        });
    }
};
