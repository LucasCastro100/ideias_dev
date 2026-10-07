<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('client_accounts', function (Blueprint $table) {
            $table->string('recurring_group_id', 36)->nullable()->after('notes');
            $table->integer('recurring_until_month')->nullable()->after('recurring_group_id');
            $table->integer('recurring_until_year')->nullable()->after('recurring_until_month');
        });
    }

    public function down(): void
    {
        Schema::table('client_accounts', function (Blueprint $table) {
            $table->dropColumn(['recurring_group_id', 'recurring_until_month', 'recurring_until_year']);
        });
    }
};
