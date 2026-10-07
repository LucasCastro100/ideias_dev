<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $tables = ['clients', 'client_accounts', 'lesson_logs', 'plans'];

        foreach ($tables as $table) {
            Schema::table($table, function (Blueprint $t) {
                $t->foreignId('team_id')->nullable()->after('user_id')->constrained('teams')->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        $tables = ['clients', 'client_accounts', 'lesson_logs', 'plans'];

        foreach ($tables as $table) {
            Schema::table($table, function (Blueprint $t) {
                $t->dropForeign(['team_id']);
                $t->dropColumn('team_id');
            });
        }
    }
};
