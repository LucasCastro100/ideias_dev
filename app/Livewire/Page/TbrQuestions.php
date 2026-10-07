<?php

namespace App\Livewire\Page;

use Livewire\Component;
use Laravel\Jetstream\InteractsWithBanner;
use App\Models\AssessmentQuestion;

class TbrQuestions extends Component
{
    use InteractsWithBanner;

    public $title = 'TBR - Perguntas';

    public $showCreateModal = false;
    public $showEditModal = false;
    public $showDeleteModal = false;

    public $editId;
    public $editLevel = 'basic';
    public $editModalitySlug = 'ap';
    public $editObjectName;
    public $editImage = '';
    public $editMission = false;
    public $editCriteria = [];
    public $newCriterion = '';

    public $deleteTarget;
    public $filterLevel = '';
    public $filterModality = '';

    public function render()
    {
        $query = AssessmentQuestion::orderBy('level')->orderBy('modality_slug')->orderBy('sort_order');

        if ($this->filterLevel) {
            $query->where('level', $this->filterLevel);
        }
        if ($this->filterModality) {
            $query->where('modality_slug', $this->filterModality);
        }

        return view('livewire.page.tbr.questions', [
            'questions' => $query->get(),
        ])->layout('layouts.app-sidebar', [
            'showSidebar' => true,
            'title' => $this->title,
        ]);
    }

    public function openCreateModal()
    {
        $this->authorize('create');
        $this->resetForm();
        $this->editCriteria = [''];
        $this->showCreateModal = true;
    }

    public function closeCreateModal()
    {
        $this->showCreateModal = false;
        $this->resetForm();
    }

    public function addCriterion()
    {
        $this->editCriteria[] = '';
    }

    public function removeCriterion($index)
    {
        unset($this->editCriteria[$index]);
        $this->editCriteria = array_values($this->editCriteria);
    }

    public function save()
    {
        $this->authorize('create');
        $this->validate([
            'editLevel' => 'required',
            'editModalitySlug' => 'required',
            'editObjectName' => 'required|min:2',
            'editCriteria' => 'required|array|min:1',
            'editCriteria.*' => 'required|string',
        ]);

        $maxSort = AssessmentQuestion::where('level', $this->editLevel)
            ->where('modality_slug', $this->editModalitySlug)
            ->max('sort_order') ?? 0;

        AssessmentQuestion::create([
            'level' => $this->editLevel,
            'modality_slug' => $this->editModalitySlug,
            'object_name' => $this->editObjectName,
            'image' => $this->editImage ?? '',
            'mission' => $this->editMission,
            'criteria' => array_values(array_filter($this->editCriteria)),
            'sort_order' => $maxSort + 1,
        ]);

        $this->banner('Pergunta criada com sucesso!');
        $this->closeCreateModal();
    }

    public function openEditModal($id)
    {
        $this->authorize('edit');
        $q = AssessmentQuestion::findOrFail($id);
        $this->editId = $q->id;
        $this->editLevel = $q->level;
        $this->editModalitySlug = $q->modality_slug;
        $this->editObjectName = $q->object_name;
        $this->editImage = $q->image ?? '';
        $this->editMission = $q->mission;
        $this->editCriteria = $q->criteria ?: [''];
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
            'editModalitySlug' => 'required',
            'editObjectName' => 'required|min:2',
            'editCriteria' => 'required|array|min:1',
            'editCriteria.*' => 'required|string',
        ]);

        AssessmentQuestion::findOrFail($this->editId)->update([
            'level' => $this->editLevel,
            'modality_slug' => $this->editModalitySlug,
            'object_name' => $this->editObjectName,
            'image' => $this->editImage ?? '',
            'mission' => $this->editMission,
            'criteria' => array_values(array_filter($this->editCriteria)),
        ]);

        $this->banner('Pergunta atualizada com sucesso!');
        $this->closeEditModal();
    }

    public function openDeleteModal($id)
    {
        $this->authorize('delete');
        $this->deleteTarget = AssessmentQuestion::findOrFail($id);
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
        $this->banner('Pergunta removida com sucesso!');
        $this->closeDeleteModal();
    }

    private function resetForm()
    {
        $this->editId = null;
        $this->editLevel = 'basic';
        $this->editModalitySlug = 'ap';
        $this->editObjectName = '';
        $this->editImage = '';
        $this->editMission = false;
        $this->editCriteria = [];
        $this->newCriterion = '';
    }
}
