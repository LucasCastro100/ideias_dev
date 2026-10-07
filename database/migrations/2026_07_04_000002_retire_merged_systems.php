<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * slug que some => slug que sobrevive, depois da fusão de sistemas:
     * faturamento-hospedagem -> financeiro, gestao-arquivos-clientes -> armazenamento-nuvem,
     * email-marketing -> marketing-multinivel.
     */
    private array $merges = [
        'faturamento-hospedagem' => 'financeiro',
        'gestao-arquivos-clientes' => 'armazenamento-nuvem',
        'email-marketing' => 'marketing-multinivel',
    ];

    public function up(): void
    {
        foreach ($this->merges as $retiredSlug => $survivorSlug) {
            $retired = DB::table('systems')->where('slug', $retiredSlug)->first();
            $survivor = DB::table('systems')->where('slug', $survivorSlug)->first();

            if (!$retired || !$survivor) {
                continue;
            }

            DB::table('users')->where('system_id', $retired->id)->update(['system_id' => $survivor->id]);
            DB::table('projects')->where('system_slug', $retiredSlug)->delete();
            DB::table('systems')->where('id', $retired->id)->delete();
        }
    }

    public function down(): void
    {
        // Fusão de sistemas não é reversível automaticamente (dados de reatribuição de usuário são destrutivos).
    }
};
