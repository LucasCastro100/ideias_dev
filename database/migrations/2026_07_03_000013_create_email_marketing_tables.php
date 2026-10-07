<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('email_lists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('email_subscribers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('list_id')->constrained('email_lists')->cascadeOnDelete();
            $table->string('email');
            $table->string('name')->nullable();
            $table->boolean('confirmed')->default(false);
            $table->boolean('unsubscribed')->default(false);
            $table->timestamps();
        });

        Schema::create('email_campaigns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('list_id')->nullable()->constrained('email_lists')->nullOnDelete();
            $table->string('subject');
            $table->text('body')->nullable();
            $table->string('status')->default('rascunho');
            $table->dateTime('sent_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('email_campaigns');
        Schema::dropIfExists('email_subscribers');
        Schema::dropIfExists('email_lists');
    }
};
