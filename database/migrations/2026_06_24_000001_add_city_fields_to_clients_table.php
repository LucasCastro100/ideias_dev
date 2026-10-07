<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->string('bairro', 100)->nullable()->after('endereco');
            $table->string('cidade', 100)->nullable()->after('bairro');
            $table->char('uf', 2)->nullable()->after('cidade');
        });
    }

    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn(['bairro', 'cidade', 'uf']);
        });
    }
};
