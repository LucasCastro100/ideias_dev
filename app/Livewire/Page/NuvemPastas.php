<?php

namespace App\Livewire\Page;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\NuvemPasta;
use Laravel\Jetstream\InteractsWithBanner;

class NuvemPastas extends Component
{
    use InteractsWithBanner, WithPagination;

    public $showModal = false;
    public $folderId = null;
    public $name = '';
    public $parent_id = '';
    public $perPage = 10;

    public $availableParents;

    public $confirmingId = null;
    public $confirmingMessage = '';

    public function mount()
    {
        $this->availableParents = NuvemPasta::where('user_id', auth()->id())->orderBy('name')->get();
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
        $folder = NuvemPasta::where('user_id', auth()->id())->findOrFail($id);
        $this->folderId = $folder->id;
        $this->name = $folder->name;
        $this->parent_id = $folder->parent_id;
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:nuvem_folders,id',
        ]);

        if ($this->folderId && $this->parent_id == $this->folderId) {
            $this->addError('parent_id', 'Uma pasta não pode ser pai dela mesma.');
            return;
        }

        NuvemPasta::updateOrCreate(
            ['id' => $this->folderId],
            [
                'user_id' => $this->folderId ? NuvemPasta::find($this->folderId)->user_id : auth()->id(),
                'name' => $this->name,
                'parent_id' => $this->parent_id ?: null,
            ]
        );

        $this->banner($this->folderId ? 'Pasta atualizada com sucesso!' : 'Pasta criada com sucesso!');
        $this->showModal = false;
        $this->resetForm();
        $this->availableParents = NuvemPasta::where('user_id', auth()->id())->orderBy('name')->get();
    }

    public function confirmDelete($id)
    {
        $this->confirmingId = $id;
        $this->confirmingMessage = 'Excluir esta pasta? Esta ação não pode ser desfeita.';
    }

    public function executeAction()
    {
        NuvemPasta::where('user_id', auth()->id())->findOrFail($this->confirmingId)->delete();
        $this->banner('Pasta excluída com sucesso!');
        $this->confirmingId = null;
        $this->confirmingMessage = '';
        $this->availableParents = NuvemPasta::where('user_id', auth()->id())->orderBy('name')->get();
    }

    public function cancelConfirmation()
    {
        $this->confirmingId = null;
        $this->confirmingMessage = '';
    }

    public function resetForm()
    {
        $this->folderId = null;
        $this->name = '';
        $this->parent_id = '';
    }

    public function render()
    {
        $folders = NuvemPasta::where('user_id', auth()->id())
            ->with('parent')
            ->orderBy('name')
            ->paginate($this->perPage);

        return view('livewire.page.armazenamento-nuvem.pastas', [
            'folders' => $folders,
            'availableParents' => $this->availableParents,
        ])->layout('layouts.app-sidebar', [
            'showSidebar' => true,
            'title' => 'Pastas',
        ]);
    }
}
