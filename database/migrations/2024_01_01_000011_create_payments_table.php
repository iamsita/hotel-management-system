<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reservation_id')->nullable()->constrained('reservations')->cascadeOnDelete();
            $table->foreignId('invoice_id')->nullable()->constrained('invoices')->cascadeOnDelete();
            $table->decimal('amount', 12, 2);
            $table->enum('payment_method', [
                'cash',
                'card',
                'bank_transfer',
                'check',
                'online',
            ])->default('card');
            $table->enum('status', [
                'pending',
                'completed',
                'failed',
                'refunded',
            ])->default('completed');
            $table->string('transaction_id')->nullable()->unique();
            $table->text('notes')->nullable();
            $table->foreignId('processed_by')->nullable()->constrained('users')->cascadeOnDelete();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
