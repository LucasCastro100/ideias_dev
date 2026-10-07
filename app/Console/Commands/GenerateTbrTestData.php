<?php

namespace App\Console\Commands;

use App\Models\Event;
use App\Models\Team;
use App\Models\TeamModalityScore;
use Database\Seeders\TbrReferenceSeeder;
use Illuminate\Console\Command;

class GenerateTbrTestData extends Command
{
    // Comandos de teste (já roda o seeder automaticamente):
    // php artisan tbr:generate-test --qtd=5                     (evento com notas, 5 equipes/categoria)
    // php artisan tbr:generate-test --zerado                    (evento zerado, 5 equipes/categoria)
    // php artisan tbr:generate-test --zerado --qtd=10           (evento zerado, 10 equipes/categoria)
    protected $signature = 'tbr:generate-test {--zerado : Cria evento com todas as notas zeradas} {--qtd=5 : Quantidade de equipes por categoria}';
    protected $description = 'Adiciona um novo evento com equipes e notas no banco de dados';

    public function handle()
    {
        $modoZerado = $this->option('zerado');
        $qtd = (int) $this->option('qtd');

        $this->info("--- Gerador de Dados de Teste TBR ---");

        $this->info("Verificando dados de referência...");
        $this->callSilent(TbrReferenceSeeder::class, ['--force' => true]);

        $this->info("Modo: " . ($modoZerado ? 'ZERADO' : 'COM NOTAS ALEATÓRIAS'));
        $this->info("Equipes por categoria: {$qtd}");

        $eventoId = 'EVT' . strtoupper(bin2hex(random_bytes(4)));

        $event = Event::create([
            'id' => $eventoId,
            'name' => $modoZerado
                ? 'Torneio TBR (Zerado) - ' . date('d/m/Y')
                : 'Torneio TBR (Com Notas) - ' . date('d/m/Y'),
            'date' => now(),
            'status' => false,
            'location' => [
                'regiao' => ['id' => 3, 'sigla' => 'SE', 'nome' => 'Sudeste'],
                'estado' => ['id' => 31, 'sigla' => 'MG', 'nome' => 'Minas Gerais'],
                'municipio' => ['id' => 3170206, 'nome' => 'Uberlândia'],
            ],
        ]);

        $categorias = [
            'kids1' => 'Kids 1', 'kids2' => 'Kids 2', 'middle1' => 'Middle 1',
            'middle2' => 'Middle 2', 'high' => 'High', 'technic_university' => 'Technic',
        ];

        foreach ($categorias as $slugCat => $labelCat) {
            for ($i = 1; $i <= $qtd; $i++) {
                $teamId = 'TEAM' . strtoupper(bin2hex(random_bytes(4)));

                $team = Team::create([
                    'id' => $teamId,
                    'event_id' => $eventoId,
                    'name' => "{$labelCat} Equipe {$i}",
                    'category_slug' => $slugCat,
                    'total_score' => 0,
                ]);

                $modalities = ['mc', 'om', 'te'];
                $totalGeral = 0;

                foreach ($modalities as $mod) {
                    if ($modoZerado) {
                        $notas = [];
                        $total = '0.00';
                    } else {
                        $notas = [rand(5, 10)];
                        $total = number_format($notas[0], 2, '.', '');
                    }

                    TeamModalityScore::create([
                        'team_id' => $teamId,
                        'modality_slug' => $mod,
                        'round' => null,
                        'scores' => $notas,
                        'total' => $total,
                    ]);

                    $totalGeral += (float) $total;
                }

                // DP scores (3 rounds)
                if ($modoZerado) {
                    $dpRounds = [
                        'r1' => [],
                        'r2' => [],
                        'r3' => [],
                    ];
                    $totalDP = 0;
                } else {
                    $dpRounds = [];
                    foreach (['r1', 'r2', 'r3'] as $round) {
                        $scores = [];
                        for ($m = 0; $m < 6; $m++) {
                            $scores[] = rand(0, 1) * rand(10, 60);
                        }
                        $dpRounds[$round] = $scores;
                    }
                    $totalDP = max(
                        array_sum($dpRounds['r1']),
                        array_sum($dpRounds['r2']),
                        array_sum($dpRounds['r3'])
                    );
                }

                foreach ($dpRounds as $round => $scores) {
                    TeamModalityScore::create([
                        'team_id' => $teamId,
                        'modality_slug' => 'dp',
                        'round' => $round,
                        'scores' => $scores,
                        'total' => number_format(array_sum($scores), 2, '.', ''),
                    ]);
                }

                $totalGeral += $totalDP;

                $team->update(['total_score' => $totalGeral]);
            }
        }

        $this->info("Evento '{$event->name}' criado com sucesso!");
        $this->newLine();
        $this->info('--------------------------------------------------');
        $this->info('COMANDOS DISPONÍVEIS:');
        $this->comment('  Adicionar com Notas (5 equipes):');
        $this->info('     php artisan tbr:generate-test --qtd=5');
        $this->comment('  Adicionar Zerado (10 equipes):');
        $this->info('     php artisan tbr:generate-test --zerado --qtd=10');
        $this->info('--------------------------------------------------');
    }
}
