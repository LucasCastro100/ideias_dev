<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('os_customers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('name');
            $table->string('document')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('address')->nullable();
            $table->timestamps();
        });

        Schema::create('os_service_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('customer_id')->nullable()->constrained('os_customers')->nullOnDelete();
            $table->string('equipment_description')->nullable();
            $table->text('defect')->nullable();
            $table->string('status')->default('aberta');
            $table->decimal('total_value', 10, 2)->nullable();
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->timestamps();
        });

        Schema::create('os_financial_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('service_order_id')->nullable()->constrained('os_service_orders')->nullOnDelete();
            $table->string('description');
            $table->decimal('amount', 10, 2);
            $table->date('due_date')->nullable();
            $table->boolean('paid')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('os_financial_entries');
        Schema::dropIfExists('os_service_orders');
        Schema::dropIfExists('os_customers');
    }
};
