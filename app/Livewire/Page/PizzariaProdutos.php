<?php

namespace App\Livewire\Page;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\PizzariaProduto;
use Laravel\Jetstream\InteractsWithBanner;

class PizzariaProdutos extends Component
{
    use InteractsWithBanner, WithPagination;

    public $showModal = false;
    public $produtoId = null;
    public $name = '';
    public $category = '';
    public $price = '';
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
        $produto = PizzariaProduto::where('user_id', auth()->id())->findOrFail($id);
        $this->produtoId = $produto->id;
        $this->name = $produto->name;
        $this->category = $produto->category;
        $this->price = $produto->price;
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'category' => 'nullable|string|max:255',
            'price' => 'required|numeric|min:0',
        ]);

        PizzariaProduto::updateOrCreate(
            ['id' => $this->produtoId],
            [
                'user_id' => $this->produtoId ? PizzariaProduto::find($this->produtoId)->user_id : auth()->id(),
                'name' => $this->name,
                'category' => $this->category ?: null,
                'price' => $this->price,
            ]
        );

        $this->banner($this->produtoId ? 'Produto atualizado com sucesso!' : 'Produto cadastrado com sucesso!');
        $this->showModal = false;
        $this->resetForm();
    }

    public function confirmDelete($id)
    {
        $this->confirmingId = $id;
        $this->confirmingMessage = 'Excluir este produto? Esta ação não pode ser desfeita.';
    }

    public function executeAction()
    {
        PizzariaProduto::where('user_id', auth()->id())->findOrFail($this->confirmingId)->delete();
        $this->banner('Produto excluído com sucesso!');
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
        $this->produtoId = null;
        $this->name = '';
        $this->category = '';
        $this->price = '';
    }

    public function render()
    {
        $query = PizzariaProduto::where('user_id', auth()->id());

        return view('livewire.page.pizzaria-delivery.produtos', [
            'produtos' => $query->orderBy('name')->paginate($this->perPage),
        ])->layout('layouts.app-sidebar', [
            'showSidebar' => true,
            'title' => 'Produtos',
        ]);
    }
}
