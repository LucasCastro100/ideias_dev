<?php

namespace App\Livewire\Page;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\PdvProduto;
use Laravel\Jetstream\InteractsWithBanner;

class PdvProdutos extends Component
{
    use InteractsWithBanner, WithPagination;

    public $showModal = false;
    public $produtoId = null;
    public $name = '';
    public $code = '';
    public $price = '';
    public $cost = '';
    public $stock = 0;
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
        $produto = PdvProduto::where('user_id', auth()->id())->findOrFail($id);
        $this->produtoId = $produto->id;
        $this->name = $produto->name;
        $this->code = $produto->code;
        $this->price = $produto->price;
        $this->cost = $produto->cost;
        $this->stock = $produto->stock;
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:255',
            'price' => 'required|numeric|min:0',
            'cost' => 'nullable|numeric|min:0',
            'stock' => 'required|integer|min:0',
        ]);

        PdvProduto::updateOrCreate(
            ['id' => $this->produtoId],
            [
                'user_id' => $this->produtoId ? PdvProduto::find($this->produtoId)->user_id : auth()->id(),
                'name' => $this->name,
                'code' => $this->code ?: null,
                'price' => $this->price,
                'cost' => $this->cost ?: null,
                'stock' => $this->stock,
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
        PdvProduto::where('user_id', auth()->id())->findOrFail($this->confirmingId)->delete();
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
        $this->code = '';
        $this->price = '';
        $this->cost = '';
        $this->stock = 0;
    }

    public function render()
    {
        $query = PdvProduto::where('user_id', auth()->id());

        return view('livewire.page.pdv-vendas.produtos', [
            'produtos' => $query->orderBy('name')->paginate($this->perPage),
        ])->layout('layouts.app-sidebar', [
            'showSidebar' => true,
            'title' => 'Produtos',
        ]);
    }
}
