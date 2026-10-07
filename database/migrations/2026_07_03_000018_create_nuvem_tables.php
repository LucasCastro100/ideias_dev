<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nuvem_folders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('name');
            $table->foreignId('parent_id')->nullable()->constrained('nuvem_folders')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('nuvem_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('folder_id')->nullable()->constrained('nuvem_folders')->nullOnDelete();
            $table->string('name');
            $table->integer('size_kb')->nullable();
            $table->boolean('is_public')->default(false);
            $table->string('share_token')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nuvem_files');
        Schema::dropIfExists('nuvem_folders');
    }
};
