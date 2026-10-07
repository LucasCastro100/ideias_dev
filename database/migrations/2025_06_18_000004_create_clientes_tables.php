<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('document', 20)->nullable();
            $table->text('address')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('value', 10, 2);
            $table->string('billing_cycle', 20)->default('monthly');
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        Schema::create('client_plan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('client_id')->constrained('clients')->cascadeOnDelete();
            $table->foreignId('plan_id')->constrained('plans')->cascadeOnDelete();
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->date('cancelled_at')->nullable();
            $table->boolean('active')->default(true);
            $table->decimal('adjusted_value', 10, 2)->nullable();
            $table->timestamps();

            $table->unique(['client_id', 'plan_id', 'start_date']);
        });

        Schema::create('client_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('client_id')->constrained('clients')->cascadeOnDelete();
            $table->foreignId('plan_id')->nullable()->constrained('plans')->nullOnDelete();
            $table->string('description');
            $table->decimal('value', 10, 2);
            $table->date('due_date');
            $table->date('paid_date')->nullable();
            $table->boolean('paid')->default(false);
            $table->string('category', 50)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('client_accounts');
        Schema::dropIfExists('client_plan');
        Schema::dropIfExists('plans');
        Schema::dropIfExists('clients');
    }
};
