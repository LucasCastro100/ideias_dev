<?php

namespace App\Livewire\Page;

use Livewire\Component;
use App\Models\EmpregoCompany;
use App\Models\EmpregoJob;
use App\Models\EmpregoJobSeeker;
use App\Models\EmpregoApplication;

class EmpregoDashboard extends Component
{
    public $totalCompanies = 0;
    public $totalOpenJobs = 0;
    public $totalJobSeekers = 0;
    public $totalApplications = 0;

    public function mount()
    {
        $this->loadStats();
    }

    public function loadStats()
    {
        $userId = auth()->id();

        $this->totalCompanies = EmpregoCompany::where('user_id', $userId)->count();

        $this->totalOpenJobs = EmpregoJob::where('user_id', $userId)
            ->where('status', 'aberta')
            ->count();

        $this->totalJobSeekers = EmpregoJobSeeker::where('user_id', $userId)->count();

        $this->totalApplications = EmpregoApplication::where('user_id', $userId)->count();
    }

    public function render()
    {
        return view('livewire.page.vagas-emprego.dashboard')
            ->layout('layouts.app-sidebar', [
                'showSidebar' => true,
                'title' => 'Dashboard Vagas de Emprego',
            ]);
    }
}
