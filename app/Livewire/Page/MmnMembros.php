<?php

namespace App\Livewire\Page;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\MmnMembro;
use Laravel\Jetstream\InteractsWithBanner;

class MmnMembros extends Component
{
    use InteractsWithBanner, WithPagination;

    public $showModal = false;
    public $membroId = null;
    public $sponsor_id = '';
    public $name = '';
    public $email = '';
    public $phone = '';
    public $level = 1;
    public $status = 'ativo';
    public $perPage = 10;

    public $allMembros;

    public $confirmingId = null;
    public $confirmingMessage = '';

    public function mount()
    {
        $this->allMembros = MmnMembro::where('user_id', auth()->id())->orderBy('name')->get();
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
        $membro = MmnMembro::where('user_id', auth()->id())->findOrFail($id);
        $this->membroId = $membro->id;
        $this->sponsor_id = $membro->sponsor_id;
        $this->name = $membro->name;
        $this->email = $membro->email;
        $this->phone = $membro->phone;
        $this->level = $membro->level;
        $this->status = $membro->status;
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate([
            'sponsor_id' => 'nullable|exists:mmn_members,id',
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'level' => 'required|integer|min:1',
            'status' => 'required|in:ativo,inativo,pendente',
        ]);

        MmnMembro::updateOrCreate(
            ['id' => $this->membroId],
            [
                'user_id' => $this->membroId ? MmnMembro::find($this->membroId)->user_id : auth()->id(),
                'sponsor_id' => $this->sponsor_id ?: null,
                'name' => $this->name,
                'email' => $this->email ?: null,
                'phone' => $this->phone ?: null,
                'level' => $this->level,
                'status' => $this->status,
            ]
        );

        $this->banner($this->membroId ? 'Membro atualizado com sucesso!' : 'Membro cadastrado com sucesso!');
        $this->showModal = false;
        $this->resetForm();
        $this->allMembros = MmnMembro::where('user_id', auth()->id())->orderBy('name')->get();
    }

    public function confirmDelete($id)
    {
        $this->confirmingId = $id;
        $this->confirmingMessage = 'Excluir este membro? Esta ação não pode ser desfeita.';
    }

    public function executeAction()
    {
        MmnMembro::where('user_id', auth()->id())->findOrFail($this->confirmingId)->delete();
        $this->banner('Membro excluído com sucesso!');
        $this->confirmingId = null;
        $this->confirmingMessage = '';
        $this->allMembros = MmnMembro::where('user_id', auth()->id())->orderBy('name')->get();
    }

    public function cancelConfirmation()
    {
        $this->confirmingId = null;
        $this->confirmingMessage = '';
    }

    public function resetForm()
    {
        $this->membroId = null;
        $this->sponsor_id = '';
        $this->name = '';
        $this->email = '';
        $this->phone = '';
        $this->level = 1;
        $this->status = 'ativo';
    }

    public function render()
    {
        $query = MmnMembro::where('user_id', auth()->id());

        return view('livewire.page.marketing-multinivel.membros', [
            'membros' => $query->with('sponsor')->orderBy('name')->paginate($this->perPage),
            'sponsors' => $this->allMembros,
        ])->layout('layouts.app-sidebar', [
            'showSidebar' => true,
            'title' => 'Membros',
        ]);
    }
}
