<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('receipt_vouchers', function (Blueprint $table) {
            $table->foreignId('booking_id')->nullable()->after('client_id')->constrained('bookings')->nullOnDelete();
            $table->string('payment_type', 20)->nullable()->after('amount');
            $table->index('booking_id');
        });

        Schema::table('refund_vouchers', function (Blueprint $table) {
            $table->foreignId('booking_id')->nullable()->after('supplier_id')->constrained('bookings')->nullOnDelete();
            $table->index('booking_id');
        });

        Schema::dropIfExists('payments');
    }

    public function down(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->cascadeOnDelete();
            $table->string('type');
            $table->string('method');
            $table->decimal('amount', 12, 2);
            $table->date('paid_at');
            $table->string('reference')->nullable();
            $table->string('bank_name')->nullable();
            $table->foreignId('received_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::table('refund_vouchers', function (Blueprint $table) {
            $table->dropForeign(['booking_id']);
            $table->dropIndex(['booking_id']);
            $table->dropColumn('booking_id');
        });

        Schema::table('receipt_vouchers', function (Blueprint $table) {
            $table->dropForeign(['booking_id']);
            $table->dropIndex(['booking_id']);
            $table->dropColumn(['booking_id', 'payment_type']);
        });
    }
};
