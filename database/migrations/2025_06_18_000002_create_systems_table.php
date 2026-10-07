<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('systems', function (Blueprint $table) {
            $table->id();
            $table->string('slug', 30)->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('system_id')->nullable()->constrained('systems')->nullOnDelete();
        });

        DB::table('systems')->insert([
            ['slug' => 'tbr', 'name' => 'TBR', 'description' => 'Sistema de gestão de eventos TBR', 'active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['slug' => 'financeiro', 'name' => 'Controle Financeiro', 'description' => 'Sistema de controle financeiro', 'active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['slug' => 'clientes', 'name' => 'Controle de Clientes', 'description' => 'Sistema de gestão de clientes e planos', 'active' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('system_id');
        });

        Schema::dropIfExists('systems');
    }
};
