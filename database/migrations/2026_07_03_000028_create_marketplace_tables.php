<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('marketplace_listings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('listing_type')->default('fixo');
            $table->decimal('price', 10, 2)->nullable();
            $table->decimal('current_bid', 10, 2)->nullable();
            $table->string('status')->default('ativo');
            $table->dateTime('ends_at')->nullable();
            $table->timestamps();
        });

        Schema::create('marketplace_bids', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('listing_id')->constrained('marketplace_listings')->cascadeOnDelete();
            $table->string('bidder_name');
            $table->decimal('amount', 10, 2);
            $table->dateTime('bid_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('marketplace_bids');
        Schema::dropIfExists('marketplace_listings');
    }
};
