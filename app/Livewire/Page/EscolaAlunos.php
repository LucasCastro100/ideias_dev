<?php

namespace App\Livewire\Page;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\EscolaAluno;
use App\Models\EscolaTurma;
use Laravel\Jetstream\InteractsWithBanner;

class EscolaAlunos extends Component
{
    use InteractsWithBanner, WithPagination;

    public $showModal = false;
    public $alunoId = null;
    public $class_id = '';
    public $name = '';
    public $birth_date = '';
    public $guardian_name = '';
    public $guardian_phone = '';
    public $email = '';
    public $active = true;

    public $turmas;
    public $search = '';
    public $perPage = 10;

    public $confirmingId = null;
    public $confirmingMessage = '';

    public function mount()
    {
        $this->turmas = EscolaTurma::where('user_id', auth()->id())->orderBy('name')->get();
    }

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
        $aluno = EscolaAluno::where('user_id', auth()->id())->findOrFail($id);
        $this->alunoId = $aluno->id;
        $this->class_id = $aluno->class_id;
        $this->name = $aluno->name;
        $this->birth_date = $aluno->birth_date?->format('Y-m-d');
        $this->guardian_name = $aluno->guardian_name;
        $this->guardian_phone = $aluno->guardian_phone;
        $this->email = $aluno->email;
        $this->active = $aluno->active;
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'class_id' => 'nullable|exists:escola_classes,id',
            'birth_date' => 'nullable|date',
            'guardian_name' => 'nullable|string|max:255',
            'guardian_phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'active' => 'boolean',
        ]);

        EscolaAluno::updateOrCreate(
            ['id' => $this->alunoId],
            [
                'user_id' => $this->alunoId ? EscolaAluno::find($this->alunoId)->user_id : auth()->id(),
                'class_id' => $this->class_id ?: null,
                'name' => $this->name,
                'birth_date' => $this->birth_date ?: null,
                'guardian_name' => $this->guardian_name ?: null,
                'guardian_phone' => $this->guardian_phone ?: null,
                'email' => $this->email ?: null,
                'active' => $this->active,
            ]
        );

        $this->banner($this->alunoId ? 'Aluno atualizado com sucesso!' : 'Aluno cadastrado com sucesso!');
        $this->showModal = false;
        $this->resetForm();
    }

    public function confirmDelete($id)
    {
        $this->confirmingId = $id;
        $this->confirmingMessage = 'Excluir este aluno? Esta ação não pode ser desfeita.';
    }

    public function executeAction()
    {
        EscolaAluno::where('user_id', auth()->id())->findOrFail($this->confirmingId)->delete();
        $this->banner('Aluno excluído com sucesso!');
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
        $this->alunoId = null;
        $this->class_id = '';
        $this->name = '';
        $this->birth_date = '';
        $this->guardian_name = '';
        $this->guardian_phone = '';
        $this->email = '';
        $this->active = true;
    }

    public function render()
    {
        $query = EscolaAluno::where('user_id', auth()->id());

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                  ->orWhere('guardian_name', 'like', "%{$this->search}%");
            });
        }

        return view('livewire.page.gestao-escolar.alunos', [
            'alunos' => $query->with('turma')->orderBy('name')->paginate($this->perPage),
            'turmas' => $this->turmas,
        ])->layout('layouts.app-sidebar', [
            'showSidebar' => true,
            'title' => 'Alunos',
        ]);
    }
}
