<?php

namespace App\Livewire\Page;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\SuporteDepartamento;
use Laravel\Jetstream\InteractsWithBanner;

class SuporteDepartamentos extends Component
{
    use InteractsWithBanner, WithPagination;

    public $showModal = false;
    public $departamentoId = null;
    public $name = '';

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
        $departamento = SuporteDepartamento::where('user_id', auth()->id())->findOrFail($id);
        $this->departamentoId = $departamento->id;
        $this->name = $departamento->name;
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate([
            'name' => 'required|string|max:255',
        ]);

        SuporteDepartamento::updateOrCreate(
            ['id' => $this->departamentoId],
            [
                'user_id' => $this->departamentoId ? SuporteDepartamento::find($this->departamentoId)->user_id : auth()->id(),
                'name' => $this->name,
            ]
        );

        $this->banner($this->departamentoId ? 'Departamento atualizado com sucesso!' : 'Departamento cadastrado com sucesso!');
        $this->showModal = false;
        $this->resetForm();
    }

    public function confirmDelete($id)
    {
        $this->confirmingId = $id;
        $this->confirmingMessage = 'Excluir este departamento? Esta ação não pode ser desfeita.';
    }

    public function executeAction()
    {
        SuporteDepartamento::where('user_id', auth()->id())->findOrFail($this->confirmingId)->delete();
        $this->banner('Departamento excluído com sucesso!');
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
        $this->departamentoId = null;
        $this->name = '';
    }

    public function render()
    {
        $query = SuporteDepartamento::where('user_id', auth()->id());

        if ($this->search) {
            $query->where('name', 'like', "%{$this->search}%");
        }

        return view('livewire.page.central-suporte.departamentos', [
            'departamentos' => $query->orderBy('name')->paginate($this->perPage),
        ])->layout('layouts.app-sidebar', [
            'showSidebar' => true,
            'title' => 'Departamentos',
        ]);
    }
}
