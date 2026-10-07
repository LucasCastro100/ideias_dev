<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ClearAll extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clear:all';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Comando clear:all executa — cache, config, rotas, views, eventos e scheduler limpos.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->call('cache:clear');
        $this->call('config:clear');
        $this->call('route:clear');
        $this->call('view:clear');
        $this->call('event:clear');
        $this->call('schedule:clear-cache');
        $this->call('optimize:clear');

        Log::info('Comando clear:all executado — cache, config, rotas, views, eventos e scheduler limpos.');
    }
}
