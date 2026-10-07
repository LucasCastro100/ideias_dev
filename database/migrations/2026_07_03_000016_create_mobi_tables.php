<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mobi_drivers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('name');
            $table->string('phone')->nullable();
            $table->string('license_no')->nullable();
            $table->string('vehicle_category')->default('sedan');
            $table->string('status')->default('ativo');
            $table->timestamps();
        });

        Schema::create('mobi_rides', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('driver_id')->nullable()->constrained('mobi_drivers')->nullOnDelete();
            $table->string('rider_name');
            $table->string('pickup_address');
            $table->string('drop_address');
            $table->string('status')->default('pendente');
            $table->decimal('distance_km', 8, 2)->nullable();
            $table->decimal('amount', 10, 2)->nullable();
            $table->dateTime('requested_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mobi_rides');
        Schema::dropIfExists('mobi_drivers');
    }
};
