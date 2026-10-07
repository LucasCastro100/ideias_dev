<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('account_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('financial_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type', 20)->default('expense');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('financial_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('account_type_id')->nullable()->constrained('account_types')->nullOnDelete();
            $table->foreignId('category_id')->nullable()->constrained('financial_categories')->nullOnDelete();
            $table->string('description');
            $table->decimal('value', 10, 2);
            $table->date('due_date')->nullable();
            $table->date('paid_date')->nullable();
            $table->boolean('paid')->default(false);
            $table->integer('month');
            $table->integer('year');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('financial_transactions');
        Schema::dropIfExists('financial_categories');
        Schema::dropIfExists('account_types');
    }
};
