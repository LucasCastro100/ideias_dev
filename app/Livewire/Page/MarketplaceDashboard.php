<?php

namespace App\Livewire\Page;

use Livewire\Component;
use App\Models\MarketplaceAnuncio;
use App\Models\MarketplaceLance;

class MarketplaceDashboard extends Component
{
    public $totalAnuncios = 0;
    public $anunciosAtivos = 0;
    public $encerrandoHoje = 0;
    public $totalEmLances = 0;

    public function mount()
    {
        $this->loadStats();
    }

    public function loadStats()
    {
        $userId = auth()->id();

        $this->totalAnuncios = MarketplaceAnuncio::where('user_id', $userId)->count();

        $this->anunciosAtivos = MarketplaceAnuncio::where('user_id', $userId)
            ->where('status', 'ativo')
            ->count();

        $this->encerrandoHoje = MarketplaceAnuncio::where('user_id', $userId)
            ->whereDate('ends_at', now()->toDateString())
            ->count();

        $this->totalEmLances = MarketplaceLance::where('user_id', $userId)->sum('amount');
    }

    public function render()
    {
        return view('livewire.page.marketplace-leiloes.dashboard')
            ->layout('layouts.app-sidebar', [
                'showSidebar' => true,
                'title' => 'Dashboard Marketplace',
            ]);
    }
}
