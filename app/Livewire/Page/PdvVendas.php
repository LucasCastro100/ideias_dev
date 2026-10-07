<?php

namespace App\Livewire\Page;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\PdvVenda;
use Laravel\Jetstream\InteractsWithBanner;

class PdvVendas extends Component
{
    use InteractsWithBanner, WithPagination;

    public $showModal = false;
    public $vendaId = null;
    public $customer_name = '';
    public $total = '';
    public $discount = 0;
    public $status = 'concluida';
    public $sold_at = '';
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
        $venda = PdvVenda::where('user_id', auth()->id())->findOrFail($id);
        $this->vendaId = $venda->id;
        $this->customer_name = $venda->customer_name;
        $this->total = $venda->total;
        $this->discount = $venda->discount;
        $this->status = $venda->status;
        $this->sold_at = $venda->sold_at?->format('Y-m-d\TH:i');
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate([
            'customer_name' => 'nullable|string|max:255',
            'total' => 'required|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'status' => 'required|in:concluida,cancelada',
            'sold_at' => 'nullable|date',
        ]);

        PdvVenda::updateOrCreate(
            ['id' => $this->vendaId],
            [
                'user_id' => $this->vendaId ? PdvVenda::find($this->vendaId)->user_id : auth()->id(),
                'customer_name' => $this->customer_name ?: null,
                'total' => $this->total,
                'discount' => $this->discount ?: 0,
                'status' => $this->status,
                'sold_at' => $this->sold_at ?: null,
            ]
        );

        $this->banner($this->vendaId ? 'Venda atualizada com sucesso!' : 'Venda cadastrada com sucesso!');
        $this->showModal = false;
        $this->resetForm();
    }

    public function confirmDelete($id)
    {
        $this->confirmingId = $id;
        $this->confirmingMessage = 'Excluir esta venda? Esta ação não pode ser desfeita.';
    }

    public function executeAction()
    {
        PdvVenda::where('user_id', auth()->id())->findOrFail($this->confirmingId)->delete();
        $this->banner('Venda excluída com sucesso!');
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
        $this->vendaId = null;
        $this->customer_name = '';
        $this->total = '';
        $this->discount = 0;
        $this->status = 'concluida';
        $this->sold_at = '';
    }

    public function render()
    {
        $query = PdvVenda::where('user_id', auth()->id());

        return view('livewire.page.pdv-vendas.vendas', [
            'vendas' => $query->orderBy('created_at', 'desc')->paginate($this->perPage),
        ])->layout('layouts.app-sidebar', [
            'showSidebar' => true,
            'title' => 'Vendas',
        ]);
    }
}
