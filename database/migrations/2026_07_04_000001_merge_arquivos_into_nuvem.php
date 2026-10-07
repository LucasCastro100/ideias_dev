<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('nuvem_files', function (Blueprint $table) {
            $table->foreignId('client_id')->nullable()->after('folder_id')->constrained('arquivos_clients')->nullOnDelete();
            $table->text('description')->nullable()->after('name');
            $table->unsignedInteger('downloads_count')->default(0)->after('is_public');
        });

        if (Schema::hasTable('arquivos_files')) {
            DB::table('arquivos_files')->orderBy('id')->each(function ($file) {
                DB::table('nuvem_files')->insert([
                    'user_id' => $file->user_id,
                    'folder_id' => null,
                    'client_id' => $file->client_id,
                    'name' => $file->filename,
                    'description' => $file->description,
                    'size_kb' => null,
                    'is_public' => false,
                    'downloads_count' => $file->downloads_count ?? 0,
                    'share_token' => null,
                    'created_at' => $file->created_at,
                    'updated_at' => $file->updated_at,
                ]);
            });

            Schema::dropIfExists('arquivos_files');
        }
    }

    public function down(): void
    {
        Schema::table('nuvem_files', function (Blueprint $table) {
            $table->dropConstrainedForeignId('client_id');
            $table->dropColumn(['description', 'downloads_count']);
        });
    }
};
