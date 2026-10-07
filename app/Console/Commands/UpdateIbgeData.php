<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class UpdateIbgeData extends Command
{
    protected $signature = 'ibge:update';
    protected $description = 'Atualiza os JSON de estados e municípios a partir da API do IBGE';

    public function handle()
    {
        // Aumenta o limite de memória apenas para este processo
        ini_set('memory_limit', '512M');
        
        $this->info('🔄 Atualizando dados do IBGE...');

        $this->updateJson('https://servicodados.ibge.gov.br/api/v1/localidades/regioes', 'ibge/regioes.json', 'Regiões');
        $this->updateJson('https://servicodados.ibge.gov.br/api/v1/localidades/estados', 'ibge/estados.json', 'Estados', true);
        
        // Para municípios, usamos o stream para não estourar a memória
        $this->updateJsonStream('https://servicodados.ibge.gov.br/api/v1/localidades/municipios', 'ibge/municipios.json', 'Municípios');

        $this->info('✅ Atualização concluída!');
        return 0;
    }

    private function updateJson(string $url, string $filePath, string $label, bool $sort = false)
    {
        try {
            $response = Http::timeout(60)->get($url);

            if ($response->successful()) {
                $data = $response->json();
                
                if ($sort) {
                    usort($data, fn($a, $b) => strcmp($a['nome'], $b['nome']));
                }

                Storage::disk('public')->put(
                    $filePath,
                    json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
                );
                $this->info("✅ {$label} atualizados.");
            }
        } catch (\Exception $e) {
            $this->error("⚠️ Erro em {$label}: " . $e->getMessage());
        }
    }

    /**
     * Versão otimizada para o JSON de municípios que é muito grande
     */
    private function updateJsonStream(string $url, string $filePath, string $label)
    {
        try {
            $this->info("⏳ Baixando {$label} via stream (isso pode demorar um pouco)...");
            
            // O stream grava o conteúdo da resposta direto no sistema de arquivos
            $fp = fopen(storage_path('app/public/' . $filePath), 'w');
            
            Http::timeout(120)->sink($fp)->get($url);
            
            fclose($fp);
            $this->info("✅ {$label} atualizados via stream.");
        } catch (\Exception $e) {
            $this->error("⚠️ Erro ao atualizar {$label} via stream: " . $e->getMessage());
        }
    }
}

//