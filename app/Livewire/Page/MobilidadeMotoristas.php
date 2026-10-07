<?php

namespace App\Livewire\Page;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\MobilidadeMotorista;
use Laravel\Jetstream\InteractsWithBanner;

class MobilidadeMotoristas extends Component
{
    use InteractsWithBanner, WithPagination;

    public $showModal = false;
    public $driverId = null;
    public $name = '';
    public $phone = '';
    public $license_no = '';
    public $vehicle_category = 'sedan';
    public $status = 'ativo';
    public $perPage = 10;

    public $confirmingId = null;
    public $confirmingMessage = '';

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
        $driver = MobilidadeMotorista::where('user_id', auth()->id())->findOrFail($id);
        $this->driverId = $driver->id;
        $this->name = $driver->name;
        $this->phone = $driver->phone;
        $this->license_no = $driver->license_no;
        $this->vehicle_category = $driver->vehicle_category;
        $this->status = $driver->status;
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:30',
            'license_no' => 'nullable|string|max:50',
            'vehicle_category' => 'required|in:sedan,suv,hatch,moto',
            'status' => 'required|in:ativo,inativo',
        ]);

        MobilidadeMotorista::updateOrCreate(
            ['id' => $this->driverId],
            [
                'user_id' => $this->driverId ? MobilidadeMotorista::find($this->driverId)->user_id : auth()->id(),
                'name' => $this->name,
                'phone' => $this->phone ?: null,
                'license_no' => $this->license_no ?: null,
                'vehicle_category' => $this->vehicle_category,
                'status' => $this->status,
            ]
        );

        $this->banner($this->driverId ? 'Motorista atualizado com sucesso!' : 'Motorista cadastrado com sucesso!');
        $this->showModal = false;
        $this->resetForm();
    }

    public function confirmDelete($id)
    {
        $this->confirmingId = $id;
        $this->confirmingMessage = 'Excluir este motorista? Esta ação não pode ser desfeita.';
    }

    public function executeAction()
    {
        MobilidadeMotorista::where('user_id', auth()->id())->findOrFail($this->confirmingId)->delete();
        $this->banner('Motorista excluído com sucesso!');
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
        $this->driverId = null;
        $this->name = '';
        $this->phone = '';
        $this->license_no = '';
        $this->vehicle_category = 'sedan';
        $this->status = 'ativo';
    }

    public function render()
    {
        $drivers = MobilidadeMotorista::where('user_id', auth()->id())
            ->orderBy('name')
            ->paginate($this->perPage);

        return view('livewire.page.corridas-mobilidade.motoristas', [
            'drivers' => $drivers,
        ])->layout('layouts.app-sidebar', [
            'showSidebar' => true,
            'title' => 'Motoristas',
        ]);
    }
}
