<?php

namespace App\Livewire\Page;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\AccountType;
use Laravel\Jetstream\InteractsWithBanner;

class FinanceiroAccountTypes extends Component
{
    use InteractsWithBanner, WithPagination;

    public $showModal = false;
    public $typeId = null;
    public $name = '';
    public $description = '';
    public $perPage = 10;

    public $confirmingId = null;
    public $confirmingMessage = '';

    protected function rules()
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ];
    }

    public function openModal()
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function edit($id)
    {
        $type = AccountType::findOrFail($id);
        $this->typeId = $type->id;
        $this->name = $type->name;
        $this->description = $type->description;
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate();

        AccountType::updateOrCreate(
            ['id' => $this->typeId],
            ['name' => $this->name, 'description' => $this->description]
        );

        $this->banner($this->typeId ? 'Tipo de conta atualizado!' : 'Tipo de conta cadastrado!');
        $this->showModal = false;
        $this->resetForm();
    }

    public function confirmDelete($id)
    {
        $this->confirmingId = $id;
        $this->confirmingMessage = 'Excluir este tipo de conta? Esta ação não pode ser desfeita.';
    }

    public function executeAction()
    {
        AccountType::findOrFail($this->confirmingId)->delete();
        $this->banner('Tipo de conta excluído!');
        $this->confirmingId = null;
        $this->confirmingMessage = '';
    }

    public function cancelConfirmation()
    {
        $this->confirmingId = null;
        $this->confirmingMessage = '';
    }

    public function updatedPerPage()
    {
        $this->resetPage();
    }

    public function resetForm()
    {
        $this->typeId = null;
        $this->name = '';
        $this->description = '';
    }

    public function render()
    {
        return view('livewire.page.financeiro.account-types', [
            'types' => AccountType::orderBy('name')->paginate($this->perPage),
        ])
            ->layout('layouts.app-sidebar', [
                'showSidebar' => true,
                'title' => 'Tipos de Conta',
            ]);
    }
}
