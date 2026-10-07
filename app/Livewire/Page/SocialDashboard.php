<?php

namespace App\Livewire\Page;

use Livewire\Component;
use App\Models\SocialPublicacao;
use App\Models\SocialGrupo;

class SocialDashboard extends Component
{
    public $totalPosts = 0;
    public $postsThisMonth = 0;
    public $totalGroups = 0;
    public $openGroups = 0;

    public function mount()
    {
        $this->loadStats();
    }

    public function loadStats()
    {
        $userId = auth()->id();
        $now = now();

        $this->totalPosts = SocialPublicacao::where('user_id', $userId)->count();

        $this->postsThisMonth = SocialPublicacao::where('user_id', $userId)
            ->whereMonth('created_at', $now->month)
            ->whereYear('created_at', $now->year)
            ->count();

        $this->totalGroups = SocialGrupo::where('user_id', $userId)->count();

        $this->openGroups = SocialGrupo::where('user_id', $userId)
            ->where('privacy', 'aberto')->count();
    }

    public function render()
    {
        return view('livewire.page.rede-social.dashboard')
            ->layout('layouts.app-sidebar', [
                'showSidebar' => true,
                'title' => 'Dashboard Rede Social',
            ]);
    }
}
