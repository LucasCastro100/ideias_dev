<?php

namespace App\Livewire\Page;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\EmpregoCompany;
use App\Concerns\HasLocationSelect;
use Laravel\Jetstream\InteractsWithBanner;

class EmpregoEmpresas extends Component
{
    use InteractsWithBanner, WithPagination, HasLocationSelect;

    public $showModal = false;
    public $companyId = null;
    public $name = '';
    public $contact_name = '';
    public $email = '';
    public $phone = '';
    public $description = '';
    public $plan = 'free';
    public $active = true;

    public $search = '';
    public $perPage = 10;

    public $confirmingId = null;
    public $confirmingMessage = '';

    public function mount()
    {
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
        $company = EmpregoCompany::where('user_id', auth()->id())->findOrFail($id);
        $this->companyId = $company->id;
        $this->name = $company->name;
        $this->contact_name = $company->contact_name;
        $this->email = $company->email;
        $this->phone = $company->phone;
        $this->description = $company->description;
        $this->plan = $company->plan;
        $this->active = $company->active;
        $this->initLocationSelect($company->state, $company->city);
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'contact_name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'locState' => 'nullable|string|max:100',
            'locCity' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'plan' => 'nullable|string|max:50',
            'active' => 'boolean',
        ]);

        EmpregoCompany::updateOrCreate(
            ['id' => $this->companyId],
            [
                'user_id' => $this->companyId ? EmpregoCompany::find($this->companyId)->user_id : auth()->id(),
                'name' => $this->name,
                'contact_name' => $this->contact_name ?: null,
                'email' => $this->email ?: null,
                'phone' => $this->phone ?: null,
                'city' => $this->locCity ?: null,
                'state' => $this->locState ?: null,
                'description' => $this->description ?: null,
                'plan' => $this->plan ?: 'free',
                'active' => $this->active,
            ]
        );

        $this->banner($this->companyId ? 'Empresa atualizada com sucesso!' : 'Empresa cadastrada com sucesso!');
        $this->showModal = false;
        $this->resetForm();
    }

    public function confirmDelete($id)
    {
        $this->confirmingId = $id;
        $this->confirmingMessage = 'Excluir esta empresa? Esta ação não pode ser desfeita.';
    }

    public function executeAction()
    {
        EmpregoCompany::where('user_id', auth()->id())->findOrFail($this->confirmingId)->delete();
        $this->banner('Empresa excluída com sucesso!');
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
        $this->companyId = null;
        $this->name = '';
        $this->contact_name = '';
        $this->email = '';
        $this->phone = '';
        $this->description = '';
        $this->plan = 'free';
        $this->active = true;
        $this->resetLocationSelect();
    }

    public function render()
    {
        $query = EmpregoCompany::where('user_id', auth()->id());

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                  ->orWhere('email', 'like', "%{$this->search}%")
                  ->orWhere('city', 'like', "%{$this->search}%");
            });
        }

        return view('livewire.page.vagas-emprego.empresas', [
            'companies' => $query->orderBy('name')->paginate($this->perPage),
            'regions' => $this->locRegions,
            'filteredStates' => $this->locFilteredStates,
            'filteredCities' => $this->locFilteredCities,
        ])->layout('layouts.app-sidebar', [
            'showSidebar' => true,
            'title' => 'Empresas',
        ]);
    }
}
