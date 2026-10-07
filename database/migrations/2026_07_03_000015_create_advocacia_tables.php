<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('advocacia_clients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('name');
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('address')->nullable();
            $table->timestamps();
        });

        Schema::create('advocacia_cases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('client_id')->constrained('advocacia_clients')->cascadeOnDelete();
            $table->string('title');
            $table->string('case_no')->nullable();
            $table->string('court')->nullable();
            $table->string('stage')->default('inicial');
            $table->date('hearing_date')->nullable();
            $table->decimal('fees', 10, 2)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('advocacia_cases');
        Schema::dropIfExists('advocacia_clients');
    }
};
