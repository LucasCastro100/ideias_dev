<?php

namespace App\Livewire\Page;

use Livewire\Component;
use App\Models\ClinicaPaciente;
use App\Models\ClinicaConsulta;

class ClinicaDashboard extends Component
{
    public $totalPacientes = 0;
    public $consultasHoje = 0;
    public $consultasMes = 0;
    public $consultasCanceladas = 0;

    public function mount()
    {
        $this->loadStats();
    }

    public function loadStats()
    {
        $userId = auth()->id();
        $now = now();

        $this->totalPacientes = ClinicaPaciente::where('user_id', $userId)->count();

        $this->consultasHoje = ClinicaConsulta::where('user_id', $userId)
            ->whereDate('appointment_at', $now->toDateString())
            ->count();

        $this->consultasMes = ClinicaConsulta::where('user_id', $userId)
            ->whereMonth('appointment_at', $now->month)
            ->whereYear('appointment_at', $now->year)
            ->count();

        $this->consultasCanceladas = ClinicaConsulta::where('user_id', $userId)
            ->where('status', 'cancelada')
            ->count();
    }

    public function render()
    {
        return view('livewire.page.sistema-clinica.dashboard')
            ->layout('layouts.app-sidebar', [
                'showSidebar' => true,
                'title' => 'Dashboard Clínica',
            ]);
    }
}
