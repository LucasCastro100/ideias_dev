<?php

namespace App\Livewire\Page;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\LojaPedido;
use Laravel\Jetstream\InteractsWithBanner;

class LojaPedidos extends Component
{
    use InteractsWithBanner, WithPagination;

    public $showModal = false;
    public $pedidoId = null;
    public $customer_name = '';
    public $total = '';
    public $status = 'pendente';
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
        $pedido = LojaPedido::where('user_id', auth()->id())->findOrFail($id);
        $this->pedidoId = $pedido->id;
        $this->customer_name = $pedido->customer_name;
        $this->total = $pedido->total;
        $this->status = $pedido->status;
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate([
            'customer_name' => 'required|string|max:255',
            'total' => 'required|numeric|min:0',
            'status' => 'required|in:pendente,pago,enviado,entregue,cancelado',
        ]);

        LojaPedido::updateOrCreate(
            ['id' => $this->pedidoId],
            [
                'user_id' => $this->pedidoId ? LojaPedido::find($this->pedidoId)->user_id : auth()->id(),
                'customer_name' => $this->customer_name,
                'total' => $this->total,
                'status' => $this->status,
            ]
        );

        $this->banner($this->pedidoId ? 'Pedido atualizado com sucesso!' : 'Pedido cadastrado com sucesso!');
        $this->showModal = false;
        $this->resetForm();
    }

    public function confirmDelete($id)
    {
        $this->confirmingId = $id;
        $this->confirmingMessage = 'Excluir este pedido? Esta ação não pode ser desfeita.';
    }

    public function executeAction()
    {
        LojaPedido::where('user_id', auth()->id())->findOrFail($this->confirmingId)->delete();
        $this->banner('Pedido excluído com sucesso!');
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
        $this->pedidoId = null;
        $this->customer_name = '';
        $this->total = '';
        $this->status = 'pendente';
    }

    public function render()
    {
        $pedidos = LojaPedido::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage);

        return view('livewire.page.loja-virtual.pedidos', [
            'pedidos' => $pedidos,
        ])->layout('layouts.app-sidebar', [
            'showSidebar' => true,
            'title' => 'Pedidos',
        ]);
    }
}
