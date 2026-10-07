<?php

namespace App\Livewire\Page;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\EmailMktLista;
use Laravel\Jetstream\InteractsWithBanner;

class EmailMktListas extends Component
{
    use InteractsWithBanner, WithPagination;

    public $showModal = false;
    public $listaId = null;
    public $name = '';
    public $description = '';
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
        $lista = EmailMktLista::where('user_id', auth()->id())->findOrFail($id);
        $this->listaId = $lista->id;
        $this->name = $lista->name;
        $this->description = $lista->description;
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        EmailMktLista::updateOrCreate(
            ['id' => $this->listaId],
            [
                'user_id' => $this->listaId ? EmailMktLista::find($this->listaId)->user_id : auth()->id(),
                'name' => $this->name,
                'description' => $this->description,
            ]
        );

        $this->banner($this->listaId ? 'Lista atualizada com sucesso!' : 'Lista cadastrada com sucesso!');
        $this->showModal = false;
        $this->resetForm();
    }

    public function confirmDelete($id)
    {
        $this->confirmingId = $id;
        $this->confirmingMessage = 'Excluir esta lista? Esta ação não pode ser desfeita.';
    }

    public function executeAction()
    {
        EmailMktLista::where('user_id', auth()->id())->findOrFail($this->confirmingId)->delete();
        $this->banner('Lista excluída com sucesso!');
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
        $this->listaId = null;
        $this->name = '';
        $this->description = '';
    }

    public function render()
    {
        $query = EmailMktLista::where('user_id', auth()->id());

        return view('livewire.page.email-marketing.listas', [
            'listas' => $query->orderBy('name')->paginate($this->perPage),
        ])->layout('layouts.app-sidebar', [
            'showSidebar' => true,
            'title' => 'Listas',
        ]);
    }
}
