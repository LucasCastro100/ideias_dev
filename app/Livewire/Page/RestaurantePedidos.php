<?php

namespace App\Livewire\Page;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\RestaurantePedido;
use App\Models\RestauranteMesa;
use Laravel\Jetstream\InteractsWithBanner;

class RestaurantePedidos extends Component
{
    use InteractsWithBanner, WithPagination;

    public $showModal = false;
    public $pedidoId = null;
    public $table_id = '';
    public $items_summary = '';
    public $total = '';
    public $status = 'aberto';
    public $perPage = 10;

    public $mesas;

    public $confirmingId = null;
    public $confirmingMessage = '';

    public function mount()
    {
        $this->mesas = RestauranteMesa::where('user_id', auth()->id())->orderBy('name')->get();
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
        $pedido = RestaurantePedido::where('user_id', auth()->id())->findOrFail($id);
        $this->pedidoId = $pedido->id;
        $this->table_id = $pedido->table_id;
        $this->items_summary = $pedido->items_summary;
        $this->total = $pedido->total;
        $this->status = $pedido->status;
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate([
            'table_id' => 'nullable|exists:mesa_tables,id',
            'items_summary' => 'nullable|string',
            'total' => 'required|numeric|min:0',
            'status' => 'required|in:aberto,fechado',
        ]);

        RestaurantePedido::updateOrCreate(
            ['id' => $this->pedidoId],
            [
                'user_id' => $this->pedidoId ? RestaurantePedido::find($this->pedidoId)->user_id : auth()->id(),
                'table_id' => $this->table_id ?: null,
                'items_summary' => $this->items_summary ?: null,
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
        RestaurantePedido::where('user_id', auth()->id())->findOrFail($this->confirmingId)->delete();
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
        $this->table_id = '';
        $this->items_summary = '';
        $this->total = '';
        $this->status = 'aberto';
    }

    public function render()
    {
        $query = RestaurantePedido::where('user_id', auth()->id());

        return view('livewire.page.restaurante-mesas.pedidos', [
            'pedidos' => $query->with('table')->orderBy('created_at', 'desc')->paginate($this->perPage),
            'mesas' => $this->mesas,
        ])->layout('layouts.app-sidebar', [
            'showSidebar' => true,
            'title' => 'Pedidos',
        ]);
    }
}
