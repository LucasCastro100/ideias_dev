<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pizza_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('name');
            $table->string('category')->nullable();
            $table->decimal('price', 10, 2);
            $table->timestamps();
        });

        Schema::create('pizza_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('customer_name');
            $table->string('delivery_address')->nullable();
            $table->decimal('total', 10, 2)->default(0);
            $table->string('status')->default('recebido');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pizza_orders');
        Schema::dropIfExists('pizza_products');
    }
};
