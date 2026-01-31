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
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->string('room_number')->unique();
            $table->enum('room_type', [
                'single',
                'double',
                'suite',
                'deluxe',
            ])->default('single');
            $table->integer('capacity')->default(1);
            $table->decimal('price_per_night', 10, 2);
            $table->enum('status', [
                'available',
                'occupied',
                'maintenance',
                'reserved',
            ])->default('available');
            $table->enum('housekeeping_status', [
                'clean',
                'dirty',
                'in_progress',
                'inspected',
            ])->default('clean');
            $table->text('description')->nullable();
            $table->integer('floor')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};
