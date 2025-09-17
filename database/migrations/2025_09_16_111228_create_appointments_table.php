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
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('healthcare_professional_id')->constrained('healthcare_professionals')->onDelete('cascade');
            $table->timestamp('appointment_start_time');
            $table->timestamp('appointment_end_time');
            $table->enum('status', ['booked','completed','cancelled'])->default('booked');
            $table->enum('active_status', ['Y','X','N'])->default('Y');
            $table->text('description')->nullable()->comment('optional status description for completed or cancelled');
            $table->timestamps();

            // Indexes to speed up overlap checks
            // $table->index(['healthcare_professional_id','appointment_start_time']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
