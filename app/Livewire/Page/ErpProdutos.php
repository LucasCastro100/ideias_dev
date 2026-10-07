<?php

namespace App\Livewire\Page;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\ErpProduto;
use Laravel\Jetstream\InteractsWithBanner;

class ErpProdutos extends Component
{
    use InteractsWithBanner, WithPagination;

    public $showModal = false;
    public $productId = null;
    public $name = '';
    public $cost = '';
    public $price = '';
    public $stock = 0;
    public $perPage = 10;

    public $confirmingId = null;
    public $confirmingMessage = '';

    protected function rules()
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'cost' => ['nullable', 'numeric', 'min:0'],
            'price' => ['required', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
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
        $product = ErpProduto::where('user_id', auth()->id())->findOrFail($id);
        $this->productId = $product->id;
        $this->name = $product->name;
        $this->cost = $product->cost;
        $this->price = $product->price;
        $this->stock = $product->stock;
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate();

        ErpProduto::updateOrCreate(
            ['id' => $this->productId],
            [
                'user_id' => $this->productId ? ErpProduto::find($this->productId)->user_id : auth()->id(),
                'name' => $this->name,
                'cost' => $this->cost !== '' ? $this->cost : null,
                'price' => $this->price,
                'stock' => $this->stock,
            ]
        );

        $this->banner($this->productId ? 'Produto atualizado com sucesso!' : 'Produto cadastrado com sucesso!');
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
        ErpProduto::where('user_id', auth()->id())->findOrFail($this->confirmingId)->delete();
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
        $this->productId = null;
        $this->name = '';
        $this->cost = '';
        $this->price = '';
        $this->stock = 0;
    }

    public function render()
    {
        $query = ErpProduto::where('user_id', auth()->id());

        return view('livewire.page.controle-empresarial-nfe.produtos', [
            'products' => $query->orderBy('name')->paginate($this->perPage),
        ])->layout('layouts.app-sidebar', [
            'showSidebar' => true,
            'title' => 'Produtos',
        ]);
    }
}
