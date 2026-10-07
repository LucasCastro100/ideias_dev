<?php

namespace App\Livewire\Page;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\SuporteTicket;
use App\Models\SuporteDepartamento;
use Laravel\Jetstream\InteractsWithBanner;

class SuporteTickets extends Component
{
    use InteractsWithBanner, WithPagination;

    public $showModal = false;
    public $ticketId = null;
    public $department_id = '';
    public $subject = '';
    public $message = '';
    public $priority = 'media';
    public $status = 'aberto';

    public $departamentos;
    public $search = '';
    public $perPage = 10;

    public $confirmingId = null;
    public $confirmingMessage = '';

    public function mount()
    {
        $this->departamentos = SuporteDepartamento::where('user_id', auth()->id())->orderBy('name')->get();
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
        $ticket = SuporteTicket::where('user_id', auth()->id())->findOrFail($id);
        $this->ticketId = $ticket->id;
        $this->department_id = $ticket->department_id;
        $this->subject = $ticket->subject;
        $this->message = $ticket->message;
        $this->priority = $ticket->priority;
        $this->status = $ticket->status;
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
            'department_id' => 'nullable|exists:suporte_departments,id',
            'priority' => 'required|in:baixa,media,alta',
            'status' => 'required|in:aberto,pendente,resolvido',
        ]);

        SuporteTicket::updateOrCreate(
            ['id' => $this->ticketId],
            [
                'user_id' => $this->ticketId ? SuporteTicket::find($this->ticketId)->user_id : auth()->id(),
                'department_id' => $this->department_id ?: null,
                'subject' => $this->subject,
                'message' => $this->message,
                'priority' => $this->priority,
                'status' => $this->status,
            ]
        );

        $this->banner($this->ticketId ? 'Ticket atualizado com sucesso!' : 'Ticket cadastrado com sucesso!');
        $this->showModal = false;
        $this->resetForm();
    }

    public function confirmDelete($id)
    {
        $this->confirmingId = $id;
        $this->confirmingMessage = 'Excluir este ticket? Esta ação não pode ser desfeita.';
    }

    public function executeAction()
    {
        SuporteTicket::where('user_id', auth()->id())->findOrFail($this->confirmingId)->delete();
        $this->banner('Ticket excluído com sucesso!');
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
        $this->ticketId = null;
        $this->department_id = '';
        $this->subject = '';
        $this->message = '';
        $this->priority = 'media';
        $this->status = 'aberto';
    }

    public function render()
    {
        $query = SuporteTicket::where('user_id', auth()->id());

        if ($this->search) {
            $query->where('subject', 'like', "%{$this->search}%");
        }

        return view('livewire.page.central-suporte.tickets', [
            'tickets' => $query->with('department')->orderBy('created_at', 'desc')->paginate($this->perPage),
            'departamentos' => $this->departamentos,
        ])->layout('layouts.app-sidebar', [
            'showSidebar' => true,
            'title' => 'Tickets',
        ]);
    }
}
