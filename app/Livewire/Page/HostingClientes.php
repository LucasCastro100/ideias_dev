<?php

namespace App\Livewire\Page;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\HostingCliente;
use Laravel\Jetstream\InteractsWithBanner;

class HostingClientes extends Component
{
    use InteractsWithBanner, WithPagination;

    public $showModal = false;
    public $clientId = null;
    public $name = '';
    public $email = '';
    public $status = 'ativo';
    public $perPage = 10;

    public $confirmingId = null;
    public $confirmingMessage = '';

    protected function rules()
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'status' => ['required', 'in:ativo,suspenso'],
        ];
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
        $client = HostingCliente::where('user_id', auth()->id())->findOrFail($id);
        $this->clientId = $client->id;
        $this->name = $client->name;
        $this->email = $client->email;
        $this->status = $client->status;
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate();

        HostingCliente::updateOrCreate(
            ['id' => $this->clientId],
            [
                'user_id' => $this->clientId ? HostingCliente::find($this->clientId)->user_id : auth()->id(),
                'name' => $this->name,
                'email' => $this->email,
                'status' => $this->status,
            ]
        );

        $this->banner($this->clientId ? 'Cliente atualizado com sucesso!' : 'Cliente cadastrado com sucesso!');
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
        HostingCliente::where('user_id', auth()->id())->findOrFail($this->confirmingId)->delete();
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
        $this->clientId = null;
        $this->name = '';
        $this->email = '';
        $this->status = 'ativo';
    }

    public function render()
    {
        $query = HostingCliente::where('user_id', auth()->id());

        return view('livewire.page.faturamento-hospedagem.clientes', [
            'clients' => $query->orderBy('name')->paginate($this->perPage),
        ])->layout('layouts.app-sidebar', [
            'showSidebar' => true,
            'title' => 'Clientes',
        ]);
    }
}
