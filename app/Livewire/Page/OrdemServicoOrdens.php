<?php

namespace App\Livewire\Page;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\OrdemServicoOrdem;
use App\Models\OrdemServicoCliente;
use Laravel\Jetstream\InteractsWithBanner;

class OrdemServicoOrdens extends Component
{
    use InteractsWithBanner, WithPagination;

    public $showModal = false;
    public $ordemId = null;
    public $customer_id = '';
    public $equipment_description = '';
    public $defect = '';
    public $status = 'aberta';
    public $total_value = '';
    public $start_date = '';
    public $end_date = '';

    public $customers;
    public $search = '';
    public $perPage = 10;

    public $confirmingId = null;
    public $confirmingMessage = '';

    public function mount()
    {
        $this->customers = OrdemServicoCliente::where('user_id', auth()->id())->orderBy('name')->get();
        $this->start_date = now()->format('Y-m-d');
    }

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
        $ordem = OrdemServicoOrdem::where('user_id', auth()->id())->findOrFail($id);
        $this->ordemId = $ordem->id;
        $this->customer_id = $ordem->customer_id;
        $this->equipment_description = $ordem->equipment_description;
        $this->defect = $ordem->defect;
        $this->status = $ordem->status;
        $this->total_value = $ordem->total_value;
        $this->start_date = $ordem->start_date?->format('Y-m-d');
        $this->end_date = $ordem->end_date?->format('Y-m-d');
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate([
            'customer_id' => 'nullable|exists:os_customers,id',
            'equipment_description' => 'nullable|string|max:255',
            'defect' => 'nullable|string',
            'status' => 'required|in:aberta,em_andamento,concluida,cancelada',
            'total_value' => 'nullable|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date',
        ]);

        OrdemServicoOrdem::updateOrCreate(
            ['id' => $this->ordemId],
            [
                'user_id' => $this->ordemId ? OrdemServicoOrdem::find($this->ordemId)->user_id : auth()->id(),
                'customer_id' => $this->customer_id ?: null,
                'equipment_description' => $this->equipment_description ?: null,
                'defect' => $this->defect ?: null,
                'status' => $this->status,
                'total_value' => $this->total_value !== '' ? $this->total_value : null,
                'start_date' => $this->start_date,
                'end_date' => $this->end_date ?: null,
            ]
        );

        $this->banner($this->ordemId ? 'Ordem de serviço atualizada com sucesso!' : 'Ordem de serviço cadastrada com sucesso!');
        $this->showModal = false;
        $this->resetForm();
    }

    public function confirmDelete($id)
    {
        $this->confirmingId = $id;
        $this->confirmingMessage = 'Excluir esta ordem de serviço? Esta ação não pode ser desfeita.';
    }

    public function executeAction()
    {
        OrdemServicoOrdem::where('user_id', auth()->id())->findOrFail($this->confirmingId)->delete();
        $this->banner('Ordem de serviço excluída com sucesso!');
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
        $this->ordemId = null;
        $this->customer_id = '';
        $this->equipment_description = '';
        $this->defect = '';
        $this->status = 'aberta';
        $this->total_value = '';
        $this->start_date = now()->format('Y-m-d');
        $this->end_date = '';
    }

    public function render()
    {
        $query = OrdemServicoOrdem::where('user_id', auth()->id());

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('equipment_description', 'like', "%{$this->search}%")
                  ->orWhere('defect', 'like', "%{$this->search}%");
            });
        }

        return view('livewire.page.ordem-servico.ordens', [
            'ordens' => $query->with('customer')->orderBy('created_at', 'desc')->paginate($this->perPage),
            'customers' => $this->customers,
        ])->layout('layouts.app-sidebar', [
            'showSidebar' => true,
            'title' => 'Ordens de Serviço',
        ]);
    }
}
