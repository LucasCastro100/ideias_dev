<?php

namespace App\Livewire\Page;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\EmpregoJob;
use App\Models\EmpregoCompany;
use App\Concerns\HasLocationSelect;
use Laravel\Jetstream\InteractsWithBanner;

class EmpregoVagas extends Component
{
    use InteractsWithBanner, WithPagination, HasLocationSelect;

    public $showModal = false;
    public $jobId = null;
    public $company_id = '';
    public $title = '';
    public $category = '';
    public $description = '';
    public $requirements = '';
    public $salary = '';
    public $expires_at = '';
    public $status = 'aberta';

    public $companies;
    public $search = '';
    public $perPage = 10;

    public $confirmingId = null;
    public $confirmingMessage = '';

    public function mount()
    {
        $this->companies = EmpregoCompany::where('user_id', auth()->id())->orderBy('name')->get();
        $this->initLocationSelect();
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
        $job = EmpregoJob::where('user_id', auth()->id())->findOrFail($id);
        $this->jobId = $job->id;
        $this->company_id = $job->company_id;
        $this->title = $job->title;
        $this->category = $job->category;
        $this->description = $job->description;
        $this->requirements = $job->requirements;
        $this->salary = $job->salary;
        $this->expires_at = $job->expires_at?->format('Y-m-d');
        $this->status = $job->status;
        $this->initLocationSelect($job->state, $job->city);
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate([
            'title' => 'required|string|max:255',
            'company_id' => 'nullable|exists:emprego_companies,id',
            'category' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'requirements' => 'nullable|string',
            'salary' => 'nullable|numeric|min:0',
            'locState' => 'nullable|string|max:100',
            'locCity' => 'nullable|string|max:100',
            'expires_at' => 'nullable|date',
            'status' => 'required|in:aberta,pausada,encerrada',
        ]);

        EmpregoJob::updateOrCreate(
            ['id' => $this->jobId],
            [
                'user_id' => $this->jobId ? EmpregoJob::find($this->jobId)->user_id : auth()->id(),
                'company_id' => $this->company_id ?: null,
                'title' => $this->title,
                'category' => $this->category ?: null,
                'description' => $this->description ?: null,
                'requirements' => $this->requirements ?: null,
                'salary' => $this->salary !== '' ? $this->salary : null,
                'city' => $this->locCity ?: null,
                'state' => $this->locState ?: null,
                'expires_at' => $this->expires_at ?: null,
                'status' => $this->status,
            ]
        );

        $this->banner($this->jobId ? 'Vaga atualizada com sucesso!' : 'Vaga cadastrada com sucesso!');
        $this->showModal = false;
        $this->resetForm();
    }

    public function confirmDelete($id)
    {
        $this->confirmingId = $id;
        $this->confirmingMessage = 'Excluir esta vaga? Esta ação não pode ser desfeita.';
    }

    public function executeAction()
    {
        EmpregoJob::where('user_id', auth()->id())->findOrFail($this->confirmingId)->delete();
        $this->banner('Vaga excluída com sucesso!');
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
        $this->jobId = null;
        $this->company_id = '';
        $this->title = '';
        $this->category = '';
        $this->description = '';
        $this->requirements = '';
        $this->salary = '';
        $this->expires_at = '';
        $this->status = 'aberta';
        $this->resetLocationSelect();
    }

    public function render()
    {
        $query = EmpregoJob::where('user_id', auth()->id());

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('title', 'like', "%{$this->search}%")
                  ->orWhere('category', 'like', "%{$this->search}%")
                  ->orWhere('city', 'like', "%{$this->search}%");
            });
        }

        return view('livewire.page.vagas-emprego.vagas', [
            'jobs' => $query->with('company')->orderBy('created_at', 'desc')->paginate($this->perPage),
            'companies' => $this->companies,
            'regions' => $this->locRegions,
            'filteredStates' => $this->locFilteredStates,
            'filteredCities' => $this->locFilteredCities,
        ])->layout('layouts.app-sidebar', [
            'showSidebar' => true,
            'title' => 'Vagas',
        ]);
    }
}
