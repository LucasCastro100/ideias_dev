<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mmn_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->foreignId('sponsor_id')->nullable()->constrained('mmn_members')->nullOnDelete();
            $table->integer('level')->default(1);
            $table->decimal('balance', 10, 2)->default(0);
            $table->string('status')->default('ativo');
            $table->timestamps();
        });

        Schema::create('mmn_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('member_id')->constrained('mmn_members')->cascadeOnDelete();
            $table->decimal('amount', 10, 2);
            $table->string('status')->default('pendente');
            $table->dateTime('paid_at')->nullable();
            $table->text('proof_note')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mmn_payments');
        Schema::dropIfExists('mmn_members');
    }
};
