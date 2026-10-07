<?php

namespace App\Livewire\Page;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\MmnMembro;
use App\Models\MmnPagamento;
use Laravel\Jetstream\InteractsWithBanner;

class MmnPagamentos extends Component
{
    use InteractsWithBanner, WithPagination;

    public $showModal = false;
    public $pagamentoId = null;
    public $member_id = '';
    public $amount = '';
    public $status = 'pendente';
    public $proof_note = '';
    public $perPage = 10;

    public $membros;

    public $confirmingId = null;
    public $confirmingMessage = '';

    public function mount()
    {
        $this->membros = MmnMembro::where('user_id', auth()->id())->orderBy('name')->get();
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
        $pagamento = MmnPagamento::where('user_id', auth()->id())->findOrFail($id);
        $this->pagamentoId = $pagamento->id;
        $this->member_id = $pagamento->member_id;
        $this->amount = $pagamento->amount;
        $this->status = $pagamento->status;
        $this->proof_note = $pagamento->proof_note;
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate([
            'member_id' => 'required|exists:mmn_members,id',
            'amount' => 'required|numeric|min:0',
            'status' => 'required|in:pendente,aprovado,recusado',
            'proof_note' => 'nullable|string',
        ]);

        $wasApproved = false;
        if ($this->pagamentoId) {
            $current = MmnPagamento::find($this->pagamentoId);
            $wasApproved = $current->status === 'aprovado';
        }

        MmnPagamento::updateOrCreate(
            ['id' => $this->pagamentoId],
            [
                'user_id' => $this->pagamentoId ? MmnPagamento::find($this->pagamentoId)->user_id : auth()->id(),
                'member_id' => $this->member_id,
                'amount' => (float) $this->amount,
                'status' => $this->status,
                'paid_at' => $this->status === 'aprovado' ? ($wasApproved ? $current->paid_at : now()) : null,
                'proof_note' => $this->proof_note,
            ]
        );

        $this->banner($this->pagamentoId ? 'Pagamento atualizado com sucesso!' : 'Pagamento cadastrado com sucesso!');
        $this->showModal = false;
        $this->resetForm();
    }

    public function confirmDelete($id)
    {
        $this->confirmingId = $id;
        $this->confirmingMessage = 'Excluir este pagamento? Esta ação não pode ser desfeita.';
    }

    public function executeAction()
    {
        MmnPagamento::where('user_id', auth()->id())->findOrFail($this->confirmingId)->delete();
        $this->banner('Pagamento excluído com sucesso!');
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
        $this->pagamentoId = null;
        $this->member_id = '';
        $this->amount = '';
        $this->status = 'pendente';
        $this->proof_note = '';
    }

    public function render()
    {
        $query = MmnPagamento::where('user_id', auth()->id());

        return view('livewire.page.marketing-multinivel.pagamentos', [
            'pagamentos' => $query->with('member')->orderBy('created_at', 'desc')->paginate($this->perPage),
            'membros' => $this->membros,
        ])->layout('layouts.app-sidebar', [
            'showSidebar' => true,
            'title' => 'Pagamentos',
        ]);
    }
}
