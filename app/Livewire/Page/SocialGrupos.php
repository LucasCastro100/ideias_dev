<?php

namespace App\Livewire\Page;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\SocialGrupo;
use Laravel\Jetstream\InteractsWithBanner;

class SocialGrupos extends Component
{
    use InteractsWithBanner, WithPagination;

    public $showModal = false;
    public $groupId = null;
    public $name = '';
    public $description = '';
    public $privacy = 'aberto';
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
        $group = SocialGrupo::where('user_id', auth()->id())->findOrFail($id);
        $this->groupId = $group->id;
        $this->name = $group->name;
        $this->description = $group->description;
        $this->privacy = $group->privacy;
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'privacy' => 'required|in:aberto,fechado',
        ]);

        SocialGrupo::updateOrCreate(
            ['id' => $this->groupId],
            [
                'user_id' => $this->groupId ? SocialGrupo::find($this->groupId)->user_id : auth()->id(),
                'name' => $this->name,
                'description' => $this->description ?: null,
                'privacy' => $this->privacy,
            ]
        );

        $this->banner($this->groupId ? 'Grupo atualizado com sucesso!' : 'Grupo criado com sucesso!');
        $this->showModal = false;
        $this->resetForm();
    }

    public function confirmDelete($id)
    {
        $this->confirmingId = $id;
        $this->confirmingMessage = 'Excluir este grupo? Esta ação não pode ser desfeita.';
    }

    public function executeAction()
    {
        SocialGrupo::where('user_id', auth()->id())->findOrFail($this->confirmingId)->delete();
        $this->banner('Grupo excluído com sucesso!');
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
        $this->groupId = null;
        $this->name = '';
        $this->description = '';
        $this->privacy = 'aberto';
    }

    public function render()
    {
        $groups = SocialGrupo::where('user_id', auth()->id())
            ->orderBy('name')
            ->paginate($this->perPage);

        return view('livewire.page.rede-social.grupos', [
            'groups' => $groups,
        ])->layout('layouts.app-sidebar', [
            'showSidebar' => true,
            'title' => 'Grupos',
        ]);
    }
}
