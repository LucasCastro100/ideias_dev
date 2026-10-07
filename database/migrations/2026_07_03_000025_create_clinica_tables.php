<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clinica_patients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('name');
            $table->string('phone')->nullable();
            $table->date('birth_date')->nullable();
            $table->timestamps();
        });

        Schema::create('clinica_appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('patient_id')->constrained('clinica_patients')->cascadeOnDelete();
            $table->string('doctor_name')->nullable();
            $table->dateTime('appointment_at');
            $table->string('status')->default('agendada');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clinica_appointments');
        Schema::dropIfExists('clinica_patients');
    }
};
