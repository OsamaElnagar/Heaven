<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('document_sequences', function (Blueprint $table) {
            $table->id();
            $table->string('document_type', 50);
            $table->foreignId('fiscal_year_id')->constrained('fiscal_years');
            $table->string('prefix', 10);
            $table->integer('last_number')->default(0);
            $table->integer('padding')->default(5);
            $table->timestamps();
            $table->unique(['document_type', 'fiscal_year_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document_sequences');
    }
};
