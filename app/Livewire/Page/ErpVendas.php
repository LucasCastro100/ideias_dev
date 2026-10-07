<?php

namespace App\Livewire\Page;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\ErpVenda;
use Laravel\Jetstream\InteractsWithBanner;

class ErpVendas extends Component
{
    use InteractsWithBanner, WithPagination;

    public $showModal = false;
    public $saleId = null;
    public $client_name = '';
    public $total = '';
    public $nfe_number = '';
    public $sold_at = '';
    public $perPage = 10;

    public $confirmingId = null;
    public $confirmingMessage = '';

    protected function rules()
    {
        return [
            'client_name' => ['nullable', 'string', 'max:255'],
            'total' => ['required', 'numeric', 'min:0'],
            'nfe_number' => ['nullable', 'string', 'max:100'],
            'sold_at' => ['nullable', 'date'],
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
        $sale = ErpVenda::where('user_id', auth()->id())->findOrFail($id);
        $this->saleId = $sale->id;
        $this->client_name = $sale->client_name;
        $this->total = $sale->total;
        $this->nfe_number = $sale->nfe_number;
        $this->sold_at = $sale->sold_at?->format('Y-m-d\TH:i');
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate();

        ErpVenda::updateOrCreate(
            ['id' => $this->saleId],
            [
                'user_id' => $this->saleId ? ErpVenda::find($this->saleId)->user_id : auth()->id(),
                'client_name' => $this->client_name ?: null,
                'total' => $this->total,
                'nfe_number' => $this->nfe_number ?: null,
                'sold_at' => $this->sold_at ?: null,
            ]
        );

        $this->banner($this->saleId ? 'Venda atualizada com sucesso!' : 'Venda cadastrada com sucesso!');
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
        ErpVenda::where('user_id', auth()->id())->findOrFail($this->confirmingId)->delete();
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
        $this->saleId = null;
        $this->client_name = '';
        $this->total = '';
        $this->nfe_number = '';
        $this->sold_at = '';
    }

    public function render()
    {
        $query = ErpVenda::where('user_id', auth()->id());

        return view('livewire.page.controle-empresarial-nfe.vendas', [
            'sales' => $query->orderBy('sold_at', 'desc')->paginate($this->perPage),
        ])->layout('layouts.app-sidebar', [
            'showSidebar' => true,
            'title' => 'Vendas / NFe',
        ]);
    }
}
