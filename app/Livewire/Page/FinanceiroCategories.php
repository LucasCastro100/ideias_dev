<?php

namespace App\Livewire\Page;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\FinancialCategory;
use Laravel\Jetstream\InteractsWithBanner;

class FinanceiroCategories extends Component
{
    use InteractsWithBanner, WithPagination;

    public $showModal = false;
    public $catId = null;
    public $name = '';
    public $type = 'expense';
    public $description = '';
    public $color = '#6b7280';
    public $perPage = 10;

    public $confirmingId = null;
    public $confirmingMessage = '';

    protected function rules()
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:income,expense'],
            'description' => ['nullable', 'string'],
            'color' => ['nullable', 'string', 'max:9'],
        ];
    }

    public function openModal()
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function edit($id)
    {
        $cat = FinancialCategory::findOrFail($id);
        $this->catId = $cat->id;
        $this->name = $cat->name;
        $this->type = $cat->type;
        $this->description = $cat->description;
        $this->color = $cat->color ?? '#6b7280';
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate();

        FinancialCategory::updateOrCreate(
            ['id' => $this->catId],
            ['name' => $this->name, 'type' => $this->type, 'description' => $this->description, 'color' => $this->color]
        );

        $this->banner($this->catId ? 'Categoria atualizada!' : 'Categoria cadastrada!');
        $this->showModal = false;
        $this->resetForm();
    }

    public function confirmDelete($id)
    {
        $this->confirmingId = $id;
        $this->confirmingMessage = 'Excluir esta categoria? Esta ação não pode ser desfeita.';
    }

    public function executeAction()
    {
        FinancialCategory::findOrFail($this->confirmingId)->delete();
        $this->banner('Categoria excluída!');
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
        $this->catId = null;
        $this->name = '';
        $this->type = 'expense';
        $this->description = '';
        $this->color = '#6b7280';
    }

    public function render()
    {
        return view('livewire.page.financeiro.categories', [
            'categories' => FinancialCategory::orderBy('name')->paginate($this->perPage),
        ])
            ->layout('layouts.app-sidebar', [
                'showSidebar' => true,
                'title' => 'Categorias Financeiras',
            ]);
    }
}
