<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mesa_tables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('name');
            $table->integer('seats')->default(4);
            $table->string('status')->default('livre');
            $table->timestamps();
        });

        Schema::create('mesa_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('table_id')->nullable()->constrained('mesa_tables')->nullOnDelete();
            $table->text('items_summary')->nullable();
            $table->decimal('total', 10, 2)->default(0);
            $table->string('status')->default('aberto');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mesa_orders');
        Schema::dropIfExists('mesa_tables');
    }
};
