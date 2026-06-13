<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->nullable()->unique();
            $table->foreignId('account_id')->nullable()->constrained('accounts');
            $table->string('name');
            $table->string('name_en')->nullable();
            $table->string('national_id')->unique();
            $table->string('passport_number')->unique()->nullable();
            $table->date('passport_expiry')->nullable();
            $table->string('phone');
            $table->string('phone_alt')->nullable();
            $table->string('email')->nullable();
            $table->string('gender');           // Gender enum
            $table->string('marital_status');   // MaritalStatus enum
            $table->date('date_of_birth')->nullable();
            $table->string('governorate')->nullable();
            $table->text('address')->nullable();
            $table->string('mahram_name')->nullable();      // for female pilgrims
            $table->string('mahram_relation')->nullable();
            $table->string('mahram_phone')->nullable();
            $table->string('blood_type')->nullable();
            $table->text('medical_notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
