<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->string('cep', 10)->nullable()->after('document');
            $table->string('numero', 20)->nullable()->after('address');
            $table->string('complemento', 100)->nullable()->after('numero');
            $table->renameColumn('address', 'endereco');
        });
    }

    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn(['cep', 'numero', 'complemento']);
            $table->renameColumn('endereco', 'address');
        });
    }
};
