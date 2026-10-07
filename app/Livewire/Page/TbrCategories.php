<?php

namespace App\Livewire\Page;

use Livewire\Component;
use Laravel\Jetstream\InteractsWithBanner;
use App\Models\Category;
use Illuminate\Support\Str;

class TbrCategories extends Component
{
    use InteractsWithBanner;

    public $title = 'TBR - Categorias';

    public $showCreateModal = false;
    public $showEditModal = false;
    public $showDeleteModal = false;

    public $editId;
    public $editSlug;
    public $editLabel;
    public $editModalityLevel;
    public $editQuestionLevel;
    public $editDpLevel;

    public $confirmingId = null;
    public $confirmingMessage = '';

    public function render()
    {
        return view('livewire.page.tbr.categories', [
            'categories' => Category::orderBy('sort_order')->get(),
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
            'editLabel' => 'required|min:2',
            'editSlug' => 'required|alpha_dash|unique:tbr_categories,slug',
            'editModalityLevel' => 'required',
            'editQuestionLevel' => 'required',
            'editDpLevel' => 'required',
        ]);

        Category::create([
            'id' => Str::upper(Str::random(12)),
            'slug' => $this->editSlug,
            'label' => $this->editLabel,
            'modality_level' => $this->editModalityLevel,
            'question_level' => $this->editQuestionLevel,
            'dp_level' => $this->editDpLevel,
            'sort_order' => Category::max('sort_order') + 1,
        ]);

        $this->banner('Categoria criada com sucesso!');
        $this->closeCreateModal();
    }

    public function openEditModal($id)
    {
        $this->authorize('edit');
        $cat = Category::findOrFail($id);
        $this->editId = $cat->id;
        $this->editSlug = $cat->slug;
        $this->editLabel = $cat->label;
        $this->editModalityLevel = $cat->modality_level;
        $this->editQuestionLevel = $cat->question_level;
        $this->editDpLevel = $cat->dp_level;
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
            'editLabel' => 'required|min:2',
            'editSlug' => 'required|alpha_dash|unique:tbr_categories,slug,' . $this->editId . ',id',
            'editModalityLevel' => 'required',
            'editQuestionLevel' => 'required',
            'editDpLevel' => 'required',
        ]);

        Category::findOrFail($this->editId)->update([
            'slug' => $this->editSlug,
            'label' => $this->editLabel,
            'modality_level' => $this->editModalityLevel,
            'question_level' => $this->editQuestionLevel,
            'dp_level' => $this->editDpLevel,
        ]);

        $this->banner('Categoria atualizada com sucesso!');
        $this->closeEditModal();
    }

    public function confirmDelete($id)
    {
        $this->authorize('delete');
        $cat = Category::findOrFail($id);
        $this->confirmingId = $id;
        $this->confirmingMessage = "Remover a categoria {$cat->label}?";
    }

    public function executeAction()
    {
        $this->authorize('delete');
        Category::findOrFail($this->confirmingId)->delete();
        $this->banner('Categoria removida com sucesso!');
        $this->confirmingId = null;
        $this->confirmingMessage = '';
    }

    public function cancelConfirmation()
    {
        $this->confirmingId = null;
        $this->confirmingMessage = '';
    }

    public function moveUp($id)
    {
        $this->authorize('edit');
        $cat = Category::findOrFail($id);
        $prev = Category::where('sort_order', '<', $cat->sort_order)
            ->orderBy('sort_order', 'desc')->first();
        if ($prev) {
            $temp = $cat->sort_order;
            $cat->update(['sort_order' => $prev->sort_order]);
            $prev->update(['sort_order' => $temp]);
        }
    }

    public function moveDown($id)
    {
        $this->authorize('edit');
        $cat = Category::findOrFail($id);
        $next = Category::where('sort_order', '>', $cat->sort_order)
            ->orderBy('sort_order')->first();
        if ($next) {
            $temp = $cat->sort_order;
            $cat->update(['sort_order' => $next->sort_order]);
            $next->update(['sort_order' => $temp]);
        }
    }

    private function resetForm()
    {
        $this->editId = null;
        $this->editSlug = '';
        $this->editLabel = '';
        $this->editModalityLevel = 'basic';
        $this->editQuestionLevel = 'basic';
        $this->editDpLevel = 'baby';
    }
}
