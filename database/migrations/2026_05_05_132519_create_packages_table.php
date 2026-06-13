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
        Schema::create('packages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->nullable()->unique();
            $table->foreignId('type_id')->nullable()->constrained('package_types')->nullOnDelete();
            $table->string('grade');         // PackageGrade enum
            $table->year('season_year');
            $table->unsignedSmallInteger('duration_nights');
            $table->decimal('base_price', 12, 2);
            $table->unsignedInteger('total_seats');
            $table->unsignedInteger('reserved_seats')->default(0);
            $table->date('departure_date')->nullable();
            $table->date('return_date')->nullable();
            $table->text('includes')->nullable();
            $table->text('excludes')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('front_office_visible')->default(true);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('packages');
    }
};
