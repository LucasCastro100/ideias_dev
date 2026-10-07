<?php

namespace App\Livewire\Page;

use Livewire\Component;
use App\Models\Event;
use App\Models\FinancialTransaction;
use App\Models\Client;
use App\Support\NewModulesNav;
use App\Models\EmpregoJob;
use App\Models\EscolaAluno;
use App\Models\OrdemServicoOrdem;
use App\Models\MmnMembro;
use App\Models\AdvocaciaProcesso;
use App\Models\MobilidadeCorrida;
use App\Models\SocialPublicacao;
use App\Models\NuvemArquivo;
use App\Models\PdvVenda;
use App\Models\RestaurantePedido;
use App\Models\PizzariaPedido;
use App\Models\SuporteTicket;
use App\Models\CmsLead;
use App\Models\ClinicaConsulta;
use App\Models\ErpVenda;
use App\Models\MarketplaceLance;
use App\Models\LojaPedido;

class Dashboard extends Component
{
    public $systemsSearch = '';
    public $systemsOverview = [];

    public function mount()
    {
        $user = auth()->user();

        if (!$user->isSuperAdmin() && $user->system_id) {
            $redirects = [
                'tbr' => 'tbr.dashboard',
                'financeiro' => 'financeiro.dashboard',
                'clientes' => 'clientes.dashboard',
                'vagas-emprego' => 'vagas-emprego.dashboard',
                'gestao-escolar' => 'gestao-escolar.dashboard',
                'ordem-servico' => 'ordem-servico.dashboard',
                'marketing-multinivel' => 'marketing-multinivel.dashboard',
                'gestao-advocacia' => 'gestao-advocacia.dashboard',
                'corridas-mobilidade' => 'corridas-mobilidade.dashboard',
                'rede-social' => 'rede-social.dashboard',
                'armazenamento-nuvem' => 'armazenamento-nuvem.dashboard',
                'pdv-vendas' => 'pdv-vendas.dashboard',
                'restaurante-mesas' => 'restaurante-mesas.dashboard',
                'pizzaria-delivery' => 'pizzaria-delivery.dashboard',
                'central-suporte' => 'central-suporte.dashboard',
                'site-institucional-cms' => 'site-institucional-cms.dashboard',
                'sistema-clinica' => 'sistema-clinica.dashboard',
                'controle-empresarial-nfe' => 'controle-empresarial-nfe.dashboard',
                'marketplace-leiloes' => 'marketplace-leiloes.dashboard',
                'loja-virtual' => 'loja-virtual.dashboard',
            ];

            $slug = $user->system?->slug;
            if ($route = $redirects[$slug] ?? null) {
                $this->redirectRoute($route);
                return;
            }
        }

        $this->loadSystemsOverview();
    }

    public function loadSystemsOverview()
    {
        $counters = [
            'tbr' => ['count' => Event::count(), 'countLabel' => 'eventos'],
            'financeiro' => ['count' => FinancialTransaction::count(), 'countLabel' => 'lançamentos'],
            'clientes' => ['count' => Client::count(), 'countLabel' => 'clientes'],
            'vagas-emprego' => ['count' => EmpregoJob::count(), 'countLabel' => 'vagas'],
            'gestao-escolar' => ['count' => EscolaAluno::count(), 'countLabel' => 'alunos'],
            'ordem-servico' => ['count' => OrdemServicoOrdem::count(), 'countLabel' => 'ordens'],
            'marketing-multinivel' => ['count' => MmnMembro::count(), 'countLabel' => 'membros'],
            'gestao-advocacia' => ['count' => AdvocaciaProcesso::count(), 'countLabel' => 'processos'],
            'corridas-mobilidade' => ['count' => MobilidadeCorrida::count(), 'countLabel' => 'corridas'],
            'rede-social' => ['count' => SocialPublicacao::count(), 'countLabel' => 'publicações'],
            'armazenamento-nuvem' => ['count' => NuvemArquivo::count(), 'countLabel' => 'arquivos'],
            'pdv-vendas' => ['count' => PdvVenda::count(), 'countLabel' => 'vendas'],
            'restaurante-mesas' => ['count' => RestaurantePedido::count(), 'countLabel' => 'pedidos'],
            'pizzaria-delivery' => ['count' => PizzariaPedido::count(), 'countLabel' => 'pedidos'],
            'central-suporte' => ['count' => SuporteTicket::count(), 'countLabel' => 'tickets'],
            'site-institucional-cms' => ['count' => CmsLead::count(), 'countLabel' => 'leads'],
            'sistema-clinica' => ['count' => ClinicaConsulta::count(), 'countLabel' => 'consultas'],
            'controle-empresarial-nfe' => ['count' => ErpVenda::count(), 'countLabel' => 'vendas'],
            'marketplace-leiloes' => ['count' => MarketplaceLance::count(), 'countLabel' => 'lances'],
            'loja-virtual' => ['count' => LojaPedido::count(), 'countLabel' => 'pedidos'],
        ];

        $this->systemsOverview = array_map(
            fn($system) => [
                ...$system,
                'count' => $counters[$system['slug']]['count'] ?? 0,
                'countLabel' => $counters[$system['slug']]['countLabel'] ?? 'registros',
            ],
            NewModulesNav::allSystems()
        );
    }

    public function render()
    {
        $systemsOverview = collect($this->systemsOverview);

        if ($this->systemsSearch !== '') {
            $systemsOverview = $systemsOverview->filter(
                fn($s) => str_contains(mb_strtolower($s['label']), mb_strtolower($this->systemsSearch))
            );
        }

        return view('livewire.page.dashboard', [
            'systemsOverviewList' => $systemsOverview->values(),
        ])->layout('layouts.app');
    }
}
