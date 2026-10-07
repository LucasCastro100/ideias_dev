<?php

namespace App\Livewire\Page;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\EscolaTurma;
use Laravel\Jetstream\InteractsWithBanner;

class EscolaTurmas extends Component
{
    use InteractsWithBanner, WithPagination;

    public $showModal = false;
    public $turmaId = null;
    public $name = '';
    public $teacher_name = '';
    public $shift = '';

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
        $turma = EscolaTurma::where('user_id', auth()->id())->findOrFail($id);
        $this->turmaId = $turma->id;
        $this->name = $turma->name;
        $this->teacher_name = $turma->teacher_name;
        $this->shift = $turma->shift;
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'teacher_name' => 'nullable|string|max:255',
            'shift' => 'nullable|string|max:50',
        ]);

        EscolaTurma::updateOrCreate(
            ['id' => $this->turmaId],
            [
                'user_id' => $this->turmaId ? EscolaTurma::find($this->turmaId)->user_id : auth()->id(),
                'name' => $this->name,
                'teacher_name' => $this->teacher_name ?: null,
                'shift' => $this->shift ?: null,
            ]
        );

        $this->banner($this->turmaId ? 'Turma atualizada com sucesso!' : 'Turma cadastrada com sucesso!');
        $this->showModal = false;
        $this->resetForm();
    }

    public function confirmDelete($id)
    {
        $this->confirmingId = $id;
        $this->confirmingMessage = 'Excluir esta turma? Esta ação não pode ser desfeita.';
    }

    public function executeAction()
    {
        EscolaTurma::where('user_id', auth()->id())->findOrFail($this->confirmingId)->delete();
        $this->banner('Turma excluída com sucesso!');
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
        $this->turmaId = null;
        $this->name = '';
        $this->teacher_name = '';
        $this->shift = '';
    }

    public function render()
    {
        $query = EscolaTurma::where('user_id', auth()->id());

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                  ->orWhere('teacher_name', 'like', "%{$this->search}%");
            });
        }

        return view('livewire.page.gestao-escolar.turmas', [
            'turmas' => $query->orderBy('name')->paginate($this->perPage),
        ])->layout('layouts.app-sidebar', [
            'showSidebar' => true,
            'title' => 'Turmas',
        ]);
    }
}
