<?php

namespace App\Livewire\Page;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\CmsLead;
use Laravel\Jetstream\InteractsWithBanner;

class CmsLeads extends Component
{
    use InteractsWithBanner, WithPagination;

    public $showModal = false;
    public $leadId = null;
    public $name = '';
    public $email = '';
    public $message = '';
    public $status = 'novo';

    public $search = '';
    public $perPage = 10;

    public $confirmingId = null;
    public $confirmingMessage = '';

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
        $lead = CmsLead::where('user_id', auth()->id())->findOrFail($id);
        $this->leadId = $lead->id;
        $this->name = $lead->name;
        $this->email = $lead->email;
        $this->message = $lead->message;
        $this->status = $lead->status;
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'message' => 'nullable|string',
            'status' => 'required|in:novo,contatado,convertido',
        ]);

        CmsLead::updateOrCreate(
            ['id' => $this->leadId],
            [
                'user_id' => $this->leadId ? CmsLead::find($this->leadId)->user_id : auth()->id(),
                'name' => $this->name,
                'email' => $this->email,
                'message' => $this->message ?: null,
                'status' => $this->status,
            ]
        );

        $this->banner($this->leadId ? 'Lead atualizado com sucesso!' : 'Lead cadastrado com sucesso!');
        $this->showModal = false;
        $this->resetForm();
    }

    public function confirmDelete($id)
    {
        $this->confirmingId = $id;
        $this->confirmingMessage = 'Excluir este lead? Esta ação não pode ser desfeita.';
    }

    public function executeAction()
    {
        CmsLead::where('user_id', auth()->id())->findOrFail($this->confirmingId)->delete();
        $this->banner('Lead excluído com sucesso!');
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
        $this->leadId = null;
        $this->name = '';
        $this->email = '';
        $this->message = '';
        $this->status = 'novo';
    }

    public function render()
    {
        $query = CmsLead::where('user_id', auth()->id());

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                  ->orWhere('email', 'like', "%{$this->search}%");
            });
        }

        return view('livewire.page.site-institucional-cms.leads', [
            'leads' => $query->orderBy('created_at', 'desc')->paginate($this->perPage),
        ])->layout('layouts.app-sidebar', [
            'showSidebar' => true,
            'title' => 'Leads',
        ]);
    }
}
