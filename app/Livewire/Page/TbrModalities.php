<?php

namespace App\Livewire\Page;

use Livewire\Component;
use Laravel\Jetstream\InteractsWithBanner;
use App\Models\Modality;

class TbrModalities extends Component
{
    use InteractsWithBanner;

    public $title = 'TBR - Modalidades';

    public $showCreateModal = false;
    public $showEditModal = false;
    public $showDeleteModal = false;

    public $editId;
    public $editLevel = 'basic';
    public $editSlug;
    public $editLabel;
    public $editConfigId;

    public $deleteTarget;
    public $filterLevel = '';

    public function render()
    {
        $query = Modality::orderBy('level')->orderBy('sort_order');

        if ($this->filterLevel) {
            $query->where('level', $this->filterLevel);
        }

        return view('livewire.page.tbr.modalities', [
            'modalities' => $query->get(),
        ])->layout('layouts.app-sidebar', [
            'showSidebar' => true,
            'title' => $this->title,
        ]);
    }

    public function openCreateModal()
    {
        $this->authorize('create');
        $this->resetForm();
        $this->showCreateModal = true;
    }

    public function closeCreateModal()
    {
        $this->showCreateModal = false;
        $this->resetForm();
    }

    public function save()
    {
        $this->authorize('create');
        $this->validate([
            'editLevel' => 'required',
            'editSlug' => 'required|alpha_dash',
            'editLabel' => 'required|min:2',
            'editConfigId' => 'required|size:12',
        ]);

        $maxSort = Modality::where('level', $this->editLevel)->max('sort_order') ?? 0;

        Modality::create([
            'level' => $this->editLevel,
            'config_id' => $this->editConfigId,
            'slug' => $this->editSlug,
            'label' => $this->editLabel,
            'sort_order' => $maxSort + 1,
        ]);

        $this->banner('Modalidade criada com sucesso!');
        $this->closeCreateModal();
    }

    public function openEditModal($id)
    {
        $this->authorize('edit');
        $m = Modality::findOrFail($id);
        $this->editId = $m->id;
        $this->editLevel = $m->level;
        $this->editSlug = $m->slug;
        $this->editLabel = $m->label;
        $this->editConfigId = $m->config_id;
        $this->showEditModal = true;
    }

    public function closeEditModal()
    {
        $this->showEditModal = false;
        $this->resetForm();
    }

    public function update()
    {
        $this->authorize('edit');
        $this->validate([
            'editLevel' => 'required',
            'editSlug' => 'required|alpha_dash',
            'editLabel' => 'required|min:2',
            'editConfigId' => 'required|size:12',
        ]);

        Modality::findOrFail($this->editId)->update([
            'level' => $this->editLevel,
            'config_id' => $this->editConfigId,
            'slug' => $this->editSlug,
            'label' => $this->editLabel,
        ]);

        $this->banner('Modalidade atualizada com sucesso!');
        $this->closeEditModal();
    }

    public function openDeleteModal($id)
    {
        $this->authorize('delete');
        $this->deleteTarget = Modality::findOrFail($id);
        $this->showDeleteModal = true;
    }

    public function closeDeleteModal()
    {
        $this->showDeleteModal = false;
        $this->deleteTarget = null;
    }

    public function delete()
    {
        $this->authorize('delete');
        $this->deleteTarget->delete();
        $this->banner('Modalidade removida com sucesso!');
        $this->closeDeleteModal();
    }

    private function resetForm()
    {
        $this->editId = null;
        $this->editLevel = 'basic';
        $this->editSlug = '';
        $this->editLabel = '';
        $this->editConfigId = '';
    }
}
