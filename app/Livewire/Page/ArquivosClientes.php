<?php

namespace App\Livewire\Page;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\ArquivosCliente;
use Laravel\Jetstream\InteractsWithBanner;

class ArquivosClientes extends Component
{
    use InteractsWithBanner, WithPagination;

    public $showModal = false;
    public $clienteId = null;
    public $name = '';
    public $email = '';

    public $search = '';
    public $perPage = 10;

    public $confirmingId = null;
    public $confirmingMessage = '';

    public function updatedSearch()
    {
        $this->resetPage();
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
        $cliente = ArquivosCliente::where('user_id', auth()->id())->findOrFail($id);
        $this->clienteId = $cliente->id;
        $this->name = $cliente->name;
        $this->email = $cliente->email;
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
        ]);

        ArquivosCliente::updateOrCreate(
            ['id' => $this->clienteId],
            [
                'user_id' => $this->clienteId ? ArquivosCliente::find($this->clienteId)->user_id : auth()->id(),
                'name' => $this->name,
                'email' => $this->email ?: null,
            ]
        );

        $this->banner($this->clienteId ? 'Cliente atualizado com sucesso!' : 'Cliente cadastrado com sucesso!');
        $this->showModal = false;
        $this->resetForm();
    }

    public function confirmDelete($id)
    {
        $this->confirmingId = $id;
        $this->confirmingMessage = 'Excluir este cliente? Esta ação não pode ser desfeita.';
    }

    public function executeAction()
    {
        ArquivosCliente::where('user_id', auth()->id())->findOrFail($this->confirmingId)->delete();
        $this->banner('Cliente excluído com sucesso!');
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
        $this->clienteId = null;
        $this->name = '';
        $this->email = '';
    }

    public function render()
    {
        $query = ArquivosCliente::where('user_id', auth()->id());

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                  ->orWhere('email', 'like', "%{$this->search}%");
            });
        }

        return view('livewire.page.armazenamento-nuvem.clientes', [
            'clientes' => $query->orderBy('name')->paginate($this->perPage),
        ])->layout('layouts.app-sidebar', [
            'showSidebar' => true,
            'title' => 'Clientes',
        ]);
    }
}
