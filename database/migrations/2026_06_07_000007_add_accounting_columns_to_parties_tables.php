<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->string('code', 20)->nullable()->unique()->after('id');
            $table->foreignId('account_id')->nullable()->after('code')->constrained('accounts');
        });

        Schema::table('suppliers', function (Blueprint $table) {
            $table->string('code', 20)->nullable()->unique()->after('id');
            $table->foreignId('account_id')->nullable()->after('code')->constrained('accounts');
        });

        Schema::table('employees', function (Blueprint $table) {
            $table->string('code', 20)->nullable()->unique()->after('id');
            $table->foreignId('account_id')->nullable()->after('code')->constrained('accounts');
        });
    }

    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropForeign(['account_id']);
            $table->dropUnique(['code']);
            $table->dropColumn(['code', 'account_id']);
        });

        Schema::table('suppliers', function (Blueprint $table) {
            $table->dropForeign(['account_id']);
            $table->dropUnique(['code']);
            $table->dropColumn(['code', 'account_id']);
        });

        Schema::table('employees', function (Blueprint $table) {
            $table->dropForeign(['account_id']);
            $table->dropUnique(['code']);
            $table->dropColumn(['code', 'account_id']);
        });
    }
};
