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
        Schema::create('healthcare_professionals', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('about')->nullable();
            $table->boolean('available')->default(true);
            $table->enum('speciality', [
                'GeneralPhysician',
                'Gynecologist',
                'Dermatologist',
                'Pediatricians',
                'Neurologist',
                'Gastroenterologist'
            ])->nullable();
            $table->json('available_days')->nullable(); // store as JSON array
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('healthcare_professionals');
    }
};
