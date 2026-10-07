<?php

namespace App\Livewire\Page;

use Livewire\Component;
use App\Models\NuvemPasta;
use App\Models\NuvemArquivo;

class NuvemDashboard extends Component
{
    public $totalFolders = 0;
    public $totalFiles = 0;
    public $publicFiles = 0;
    public $usedSpaceMb = 0;

    public function mount()
    {
        $this->loadStats();
    }

    public function loadStats()
    {
        $userId = auth()->id();

        $this->totalFolders = NuvemPasta::where('user_id', $userId)->count();

        $this->totalFiles = NuvemArquivo::where('user_id', $userId)->count();

        $this->publicFiles = NuvemArquivo::where('user_id', $userId)
            ->where('is_public', true)->count();

        $this->usedSpaceMb = round(
            NuvemArquivo::where('user_id', $userId)->sum('size_kb') / 1024,
            2
        );
    }

    public function render()
    {
        return view('livewire.page.armazenamento-nuvem.dashboard')
            ->layout('layouts.app-sidebar', [
                'showSidebar' => true,
                'title' => 'Dashboard Armazenamento em Nuvem',
            ]);
    }
}
