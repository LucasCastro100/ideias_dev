<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class OptimizeAll extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'optimize:all';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Comando optimize:all executa — config, rotas, views e eventos recompilados.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🧹 Iniciando limpeza profunda do sistema...');
    
        $commands = [
            'cache:clear' => 'Cache limpo',
            'config:clear' => 'Configurações limpas',
            'route:clear' => 'Rotas limpas',
            'view:clear' => 'Views limpas',
            'event:clear' => 'Eventos limpos',
            'schedule:clear-cache' => 'Cache do scheduler limpo',
            'optimize:clear' => 'Otimizações limpas'
        ];
    
        foreach ($commands as $cmd => $message) {
            $this->call($cmd);
            $this->line("  <info>✔</info> {$message}");
        }
    
        Log::info('Comando clear:all executado com sucesso.');
        $this->info('✅ Limpeza concluída!');
    }
}

//php artisan optimize:all