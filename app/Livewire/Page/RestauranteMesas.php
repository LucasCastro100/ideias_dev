<?php

namespace App\Livewire\Page;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\RestauranteMesa;
use Laravel\Jetstream\InteractsWithBanner;

class RestauranteMesas extends Component
{
    use InteractsWithBanner, WithPagination;

    public $showModal = false;
    public $mesaId = null;
    public $name = '';
    public $seats = 4;
    public $status = 'livre';
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
        $mesa = RestauranteMesa::where('user_id', auth()->id())->findOrFail($id);
        $this->mesaId = $mesa->id;
        $this->name = $mesa->name;
        $this->seats = $mesa->seats;
        $this->status = $mesa->status;
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'seats' => 'required|integer|min:1',
            'status' => 'required|in:livre,ocupada',
        ]);

        RestauranteMesa::updateOrCreate(
            ['id' => $this->mesaId],
            [
                'user_id' => $this->mesaId ? RestauranteMesa::find($this->mesaId)->user_id : auth()->id(),
                'name' => $this->name,
                'seats' => $this->seats,
                'status' => $this->status,
            ]
        );

        $this->banner($this->mesaId ? 'Mesa atualizada com sucesso!' : 'Mesa cadastrada com sucesso!');
        $this->showModal = false;
        $this->resetForm();
    }

    public function confirmDelete($id)
    {
        $this->confirmingId = $id;
        $this->confirmingMessage = 'Excluir esta mesa? Esta ação não pode ser desfeita.';
    }

    public function executeAction()
    {
        RestauranteMesa::where('user_id', auth()->id())->findOrFail($this->confirmingId)->delete();
        $this->banner('Mesa excluída com sucesso!');
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
        $this->mesaId = null;
        $this->name = '';
        $this->seats = 4;
        $this->status = 'livre';
    }

    public function render()
    {
        $query = RestauranteMesa::where('user_id', auth()->id());

        return view('livewire.page.restaurante-mesas.mesas', [
            'mesas' => $query->orderBy('name')->paginate($this->perPage),
        ])->layout('layouts.app-sidebar', [
            'showSidebar' => true,
            'title' => 'Mesas',
        ]);
    }
}
