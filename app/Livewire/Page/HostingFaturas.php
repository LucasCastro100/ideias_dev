<?php

namespace App\Livewire\Page;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\HostingFatura;
use App\Models\HostingCliente;
use Laravel\Jetstream\InteractsWithBanner;

class HostingFaturas extends Component
{
    use InteractsWithBanner, WithPagination;

    public $showModal = false;
    public $invoiceId = null;
    public $client_id = '';
    public $amount = '';
    public $due_date = '';
    public $status = 'pendente';
    public $paid_at = '';
    public $perPage = 10;

    public $clients;

    public $confirmingId = null;
    public $confirmingMessage = '';

    protected function rules()
    {
        return [
            'client_id' => ['required', 'exists:hosting_clients,id'],
            'amount' => ['required', 'numeric', 'min:0'],
            'due_date' => ['required', 'date'],
            'status' => ['required', 'in:pendente,pago,vencido'],
            'paid_at' => ['nullable', 'date'],
        ];
    }

    public function mount()
    {
        $this->clients = HostingCliente::where('user_id', auth()->id())->orderBy('name')->get();
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
        $invoice = HostingFatura::where('user_id', auth()->id())->findOrFail($id);
        $this->invoiceId = $invoice->id;
        $this->client_id = $invoice->client_id;
        $this->amount = $invoice->amount;
        $this->due_date = $invoice->due_date?->format('Y-m-d');
        $this->status = $invoice->status;
        $this->paid_at = $invoice->paid_at?->format('Y-m-d');
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate();

        HostingFatura::updateOrCreate(
            ['id' => $this->invoiceId],
            [
                'user_id' => $this->invoiceId ? HostingFatura::find($this->invoiceId)->user_id : auth()->id(),
                'client_id' => $this->client_id,
                'amount' => $this->amount,
                'due_date' => $this->due_date,
                'status' => $this->status,
                'paid_at' => $this->paid_at ?: null,
            ]
        );

        $this->banner($this->invoiceId ? 'Fatura atualizada com sucesso!' : 'Fatura cadastrada com sucesso!');
        $this->showModal = false;
        $this->resetForm();
    }

    public function confirmDelete($id)
    {
        $this->confirmingId = $id;
        $this->confirmingMessage = 'Excluir esta fatura? Esta ação não pode ser desfeita.';
    }

    public function executeAction()
    {
        HostingFatura::where('user_id', auth()->id())->findOrFail($this->confirmingId)->delete();
        $this->banner('Fatura excluída com sucesso!');
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
        $this->invoiceId = null;
        $this->client_id = '';
        $this->amount = '';
        $this->due_date = '';
        $this->status = 'pendente';
        $this->paid_at = '';
    }

    public function render()
    {
        $query = HostingFatura::where('user_id', auth()->id())->with('client');

        return view('livewire.page.faturamento-hospedagem.faturas', [
            'invoices' => $query->orderBy('due_date', 'desc')->paginate($this->perPage),
            'clients' => $this->clients,
        ])->layout('layouts.app-sidebar', [
            'showSidebar' => true,
            'title' => 'Faturas',
        ]);
    }
}
