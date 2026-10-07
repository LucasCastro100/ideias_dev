<?php

namespace App\Livewire\Page;

use Livewire\Component;
use App\Models\CmsPagina;
use App\Models\CmsLead;

class CmsDashboard extends Component
{
    public $totalPaginasPublicadas = 0;
    public $totalPaginasRascunho = 0;
    public $totalLeads = 0;
    public $leadsMes = 0;

    public function mount()
    {
        $this->loadStats();
    }

    public function loadStats()
    {
        $userId = auth()->id();
        $now = now();

        $this->totalPaginasPublicadas = CmsPagina::where('user_id', $userId)
            ->where('published', true)
            ->count();

        $this->totalPaginasRascunho = CmsPagina::where('user_id', $userId)
            ->where('published', false)
            ->count();

        $this->totalLeads = CmsLead::where('user_id', $userId)->count();

        $this->leadsMes = CmsLead::where('user_id', $userId)
            ->whereMonth('created_at', $now->month)
            ->whereYear('created_at', $now->year)
            ->count();
    }

    public function render()
    {
        return view('livewire.page.site-institucional-cms.dashboard')
            ->layout('layouts.app-sidebar', [
                'showSidebar' => true,
                'title' => 'Dashboard Site Institucional',
            ]);
    }
}
