<?php

namespace App\Livewire\Page;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\MobilidadeCorrida;
use App\Models\MobilidadeMotorista;
use Laravel\Jetstream\InteractsWithBanner;

class MobilidadeCorridas extends Component
{
    use InteractsWithBanner, WithPagination;

    public $showModal = false;
    public $rideId = null;
    public $driver_id = '';
    public $rider_name = '';
    public $pickup_address = '';
    public $drop_address = '';
    public $status = 'pendente';
    public $distance_km = '';
    public $amount = '';
    public $requested_at = '';
    public $perPage = 10;

    public $drivers;

    public $confirmingId = null;
    public $confirmingMessage = '';

    public function mount()
    {
        $this->drivers = MobilidadeMotorista::where('user_id', auth()->id())->orderBy('name')->get();
    }

    public function updatedPerPage()
    {
        $this->resetPage();
    }

    public function openModal()
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function edit($id)
    {
        $ride = MobilidadeCorrida::where('user_id', auth()->id())->findOrFail($id);
        $this->rideId = $ride->id;
        $this->driver_id = $ride->driver_id;
        $this->rider_name = $ride->rider_name;
        $this->pickup_address = $ride->pickup_address;
        $this->drop_address = $ride->drop_address;
        $this->status = $ride->status;
        $this->distance_km = $ride->distance_km;
        $this->amount = $ride->amount;
        $this->requested_at = $ride->requested_at?->format('Y-m-d\TH:i');
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate([
            'driver_id' => 'nullable|exists:mobi_drivers,id',
            'rider_name' => 'required|string|max:255',
            'pickup_address' => 'required|string|max:255',
            'drop_address' => 'required|string|max:255',
            'status' => 'required|in:pendente,aceita,em_andamento,concluida,cancelada',
            'distance_km' => 'nullable|numeric|min:0',
            'amount' => 'nullable|numeric|min:0',
            'requested_at' => 'nullable|date',
        ]);

        MobilidadeCorrida::updateOrCreate(
            ['id' => $this->rideId],
            [
                'user_id' => $this->rideId ? MobilidadeCorrida::find($this->rideId)->user_id : auth()->id(),
                'driver_id' => $this->driver_id ?: null,
                'rider_name' => $this->rider_name,
                'pickup_address' => $this->pickup_address,
                'drop_address' => $this->drop_address,
                'status' => $this->status,
                'distance_km' => $this->distance_km !== '' ? $this->distance_km : null,
                'amount' => $this->amount !== '' ? $this->amount : null,
                'requested_at' => $this->requested_at ?: null,
            ]
        );

        $this->banner($this->rideId ? 'Corrida atualizada com sucesso!' : 'Corrida cadastrada com sucesso!');
        $this->showModal = false;
        $this->resetForm();
    }

    public function confirmDelete($id)
    {
        $this->confirmingId = $id;
        $this->confirmingMessage = 'Excluir esta corrida? Esta ação não pode ser desfeita.';
    }

    public function executeAction()
    {
        MobilidadeCorrida::where('user_id', auth()->id())->findOrFail($this->confirmingId)->delete();
        $this->banner('Corrida excluída com sucesso!');
        $this->confirmingId = null;
        $this->confirmingMessage = '';
    }

    public function cancelConfirmation()
    {
        $this->confirmingId = null;
        $this->confirmingMessage = '';
    }

    public function resetForm()
    {
        $this->rideId = null;
        $this->driver_id = '';
        $this->rider_name = '';
        $this->pickup_address = '';
        $this->drop_address = '';
        $this->status = 'pendente';
        $this->distance_km = '';
        $this->amount = '';
        $this->requested_at = '';
    }

    public function render()
    {
        $rides = MobilidadeCorrida::where('user_id', auth()->id())
            ->with('driver')
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage);

        return view('livewire.page.corridas-mobilidade.corridas', [
            'rides' => $rides,
        ])->layout('layouts.app-sidebar', [
            'showSidebar' => true,
            'title' => 'Corridas',
        ]);
    }
}
