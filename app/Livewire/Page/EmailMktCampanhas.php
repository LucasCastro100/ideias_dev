<?php

namespace App\Livewire\Page;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\EmailMktLista;
use App\Models\EmailMktCampanha;
use Laravel\Jetstream\InteractsWithBanner;

class EmailMktCampanhas extends Component
{
    use InteractsWithBanner, WithPagination;

    public $showModal = false;
    public $campanhaId = null;
    public $list_id = '';
    public $subject = '';
    public $body = '';
    public $status = 'rascunho';
    public $perPage = 10;

    public $listas;

    public $confirmingId = null;
    public $confirmingMessage = '';

    public function mount()
    {
        $this->listas = EmailMktLista::where('user_id', auth()->id())->orderBy('name')->get();
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
        $campanha = EmailMktCampanha::where('user_id', auth()->id())->findOrFail($id);
        $this->campanhaId = $campanha->id;
        $this->list_id = $campanha->list_id;
        $this->subject = $campanha->subject;
        $this->body = $campanha->body;
        $this->status = $campanha->status;
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate([
            'list_id' => 'nullable|exists:email_lists,id',
            'subject' => 'required|string|max:255',
            'body' => 'nullable|string',
            'status' => 'required|in:rascunho,agendada,enviada',
        ]);

        EmailMktCampanha::updateOrCreate(
            ['id' => $this->campanhaId],
            [
                'user_id' => $this->campanhaId ? EmailMktCampanha::find($this->campanhaId)->user_id : auth()->id(),
                'list_id' => $this->list_id ?: null,
                'subject' => $this->subject,
                'body' => $this->body,
                'status' => $this->status,
                'sent_at' => $this->status === 'enviada' ? now() : null,
            ]
        );

        $this->banner($this->campanhaId ? 'Campanha atualizada com sucesso!' : 'Campanha cadastrada com sucesso!');
        $this->showModal = false;
        $this->resetForm();
    }

    public function confirmDelete($id)
    {
        $this->confirmingId = $id;
        $this->confirmingMessage = 'Excluir esta campanha? Esta ação não pode ser desfeita.';
    }

    public function executeAction()
    {
        EmailMktCampanha::where('user_id', auth()->id())->findOrFail($this->confirmingId)->delete();
        $this->banner('Campanha excluída com sucesso!');
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
        $this->campanhaId = null;
        $this->list_id = '';
        $this->subject = '';
        $this->body = '';
        $this->status = 'rascunho';
    }

    public function render()
    {
        $query = EmailMktCampanha::where('user_id', auth()->id());

        return view('livewire.page.email-marketing.campanhas', [
            'campanhas' => $query->with('lista')->orderBy('created_at', 'desc')->paginate($this->perPage),
            'listas' => $this->listas,
        ])->layout('layouts.app-sidebar', [
            'showSidebar' => true,
            'title' => 'Campanhas',
        ]);
    }
}
