<?php

namespace App\Livewire\Page;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\OrdemServicoCliente;
use Laravel\Jetstream\InteractsWithBanner;

class OrdemServicoClientes extends Component
{
    use InteractsWithBanner, WithPagination;

    public $showModal = false;
    public $clienteId = null;
    public $name = '';
    public $document = '';
    public $phone = '';
    public $email = '';
    public $address = '';

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
        $cliente = OrdemServicoCliente::where('user_id', auth()->id())->findOrFail($id);
        $this->clienteId = $cliente->id;
        $this->name = $cliente->name;
        $this->document = $cliente->document;
        $this->phone = $cliente->phone;
        $this->email = $cliente->email;
        $this->address = $cliente->address;
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'document' => 'nullable|string|max:30',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string|max:255',
        ]);

        OrdemServicoCliente::updateOrCreate(
            ['id' => $this->clienteId],
            [
                'user_id' => $this->clienteId ? OrdemServicoCliente::find($this->clienteId)->user_id : auth()->id(),
                'name' => $this->name,
                'document' => $this->document ?: null,
                'phone' => $this->phone ?: null,
                'email' => $this->email ?: null,
                'address' => $this->address ?: null,
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
        OrdemServicoCliente::where('user_id', auth()->id())->findOrFail($this->confirmingId)->delete();
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
        $this->document = '';
        $this->phone = '';
        $this->email = '';
        $this->address = '';
    }

    public function render()
    {
        $query = OrdemServicoCliente::where('user_id', auth()->id());

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                  ->orWhere('document', 'like', "%{$this->search}%")
                  ->orWhere('email', 'like', "%{$this->search}%");
            });
        }

        return view('livewire.page.ordem-servico.clientes', [
            'clientes' => $query->orderBy('name')->paginate($this->perPage),
        ])->layout('layouts.app-sidebar', [
            'showSidebar' => true,
            'title' => 'Clientes',
        ]);
    }
}
