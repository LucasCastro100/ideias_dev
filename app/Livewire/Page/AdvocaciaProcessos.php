<?php

namespace App\Livewire\Page;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\AdvocaciaCliente;
use App\Models\AdvocaciaProcesso;
use Laravel\Jetstream\InteractsWithBanner;

class AdvocaciaProcessos extends Component
{
    use InteractsWithBanner, WithPagination;

    public $showModal = false;
    public $processoId = null;
    public $client_id = '';
    public $title = '';
    public $case_no = '';
    public $court = '';
    public $stage = 'inicial';
    public $hearing_date = '';
    public $fees = '';
    public $perPage = 10;

    public $clientes;

    public $confirmingId = null;
    public $confirmingMessage = '';

    public function mount()
    {
        $this->clientes = AdvocaciaCliente::where('user_id', auth()->id())->orderBy('name')->get();
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
        $processo = AdvocaciaProcesso::where('user_id', auth()->id())->findOrFail($id);
        $this->processoId = $processo->id;
        $this->client_id = $processo->client_id;
        $this->title = $processo->title;
        $this->case_no = $processo->case_no;
        $this->court = $processo->court;
        $this->stage = $processo->stage;
        $this->hearing_date = $processo->hearing_date?->format('Y-m-d');
        $this->fees = $processo->fees;
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate([
            'client_id' => 'required|exists:advocacia_clients,id',
            'title' => 'required|string|max:255',
            'case_no' => 'nullable|string|max:255',
            'court' => 'nullable|string|max:255',
            'stage' => 'required|in:inicial,andamento,recurso,encerrado',
            'hearing_date' => 'nullable|date',
            'fees' => 'nullable|numeric|min:0',
        ]);

        AdvocaciaProcesso::updateOrCreate(
            ['id' => $this->processoId],
            [
                'user_id' => $this->processoId ? AdvocaciaProcesso::find($this->processoId)->user_id : auth()->id(),
                'client_id' => $this->client_id,
                'title' => $this->title,
                'case_no' => $this->case_no ?: null,
                'court' => $this->court ?: null,
                'stage' => $this->stage,
                'hearing_date' => $this->hearing_date ?: null,
                'fees' => $this->fees !== '' ? (float) $this->fees : null,
            ]
        );

        $this->banner($this->processoId ? 'Processo atualizado com sucesso!' : 'Processo cadastrado com sucesso!');
        $this->showModal = false;
        $this->resetForm();
    }

    public function confirmDelete($id)
    {
        $this->confirmingId = $id;
        $this->confirmingMessage = 'Excluir este processo? Esta ação não pode ser desfeita.';
    }

    public function executeAction()
    {
        AdvocaciaProcesso::where('user_id', auth()->id())->findOrFail($this->confirmingId)->delete();
        $this->banner('Processo excluído com sucesso!');
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
        $this->processoId = null;
        $this->client_id = '';
        $this->title = '';
        $this->case_no = '';
        $this->court = '';
        $this->stage = 'inicial';
        $this->hearing_date = '';
        $this->fees = '';
    }

    public function render()
    {
        $query = AdvocaciaProcesso::where('user_id', auth()->id());

        return view('livewire.page.gestao-advocacia.processos', [
            'processos' => $query->with('client')->orderBy('created_at', 'desc')->paginate($this->perPage),
            'clientes' => $this->clientes,
        ])->layout('layouts.app-sidebar', [
            'showSidebar' => true,
            'title' => 'Processos',
        ]);
    }
}
