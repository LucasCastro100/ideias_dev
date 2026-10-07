<?php

namespace App\Livewire\Page;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Plan;
use Laravel\Jetstream\InteractsWithBanner;

class ClientesPlans extends Component
{
    use InteractsWithBanner, WithPagination;

    public $showModal = false;
    public $planId = null;
    public $name = '';
    public $description = '';
    public $value = '';
    public $active = true;
    public $perPage = 10;

    public $confirmingId = null;
    public $confirmingMessage = '';

    protected function rules()
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'value' => ['required', 'numeric', 'min:0'],
            'active' => ['boolean'],
        ];
    }

    public function openModal()
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function edit($id)
    {
        $plan = Plan::findOrFail($id);
        $this->planId = $plan->id;
        $this->name = $plan->name;
        $this->description = $plan->description;
        $this->value = $plan->value;
        $this->active = $plan->active;
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate();

        Plan::updateOrCreate(
            ['id' => $this->planId],
            [
                'user_id' => auth()->id(),
                'name' => $this->name,
                'description' => $this->description,
                'value' => $this->value,
                'active' => $this->active,
            ]
        );

        $this->banner($this->planId ? 'Plano atualizado!' : 'Plano cadastrado!');
        $this->showModal = false;
        $this->resetForm();
    }

    public function confirmDelete($id)
    {
        $this->confirmingId = $id;
        $this->confirmingMessage = 'Excluir este plano? Esta ação não pode ser desfeita.';
    }

    public function executeAction()
    {
        Plan::findOrFail($this->confirmingId)->delete();
        $this->banner('Plano excluído!');
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
        $this->planId = null;
        $this->name = '';
        $this->description = '';
        $this->value = '';
        $this->active = true;
    }

    public function updatedPerPage()
    {
        $this->resetPage();
    }

    public function render()
    {
        return view('livewire.page.clientes.plans', [
            'plans' => Plan::orderBy('active', 'desc')->orderBy('name')->paginate($this->perPage),
        ])
            ->layout('layouts.app-sidebar', [
                'showSidebar' => true,
                'title' => 'Planos',
            ]);
    }
}
