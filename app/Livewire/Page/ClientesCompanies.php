<?php

namespace App\Livewire\Page;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Company;
use App\Models\TeamInvitation;
use App\Models\User;
use App\Notifications\CompanyInvitation;
use Illuminate\Support\Facades\Notification;
use Laravel\Jetstream\InteractsWithBanner;

class ClientesCompanies extends Component
{
    use InteractsWithBanner, WithPagination;

    public $showModal = false;
    public $showCreateModal = false;
    public $selectedCompany = null;

    public $confirmingId = null;
    public $confirmingMessage = '';

    public $search = '';
    public $perPage = 10;

    public $newCompanyName = '';

    public $inviteEmail = '';

    protected function rules()
    {
        return [
            'newCompanyName' => 'required|string|min:2|max:255',
        ];
    }

    public function openCreateModal()
    {
        $this->newCompanyName = '';
        $this->showCreateModal = true;
    }

    public function closeCreateModal()
    {
        $this->showCreateModal = false;
        $this->newCompanyName = '';
        $this->resetErrorBag('newCompanyName');
    }

    public function saveCompany()
    {
        $this->validate(['newCompanyName' => 'required|string|min:2|max:255|unique:teams,name']);

        $user = auth()->user();

        $company = Company::create([
            'user_id' => $user->id,
            'name' => $this->newCompanyName,
            'personal_team' => false,
            'system_id' => $user->system_id,
        ]);

        if (!$user->teams()->where('team_id', $company->id)->exists()) {
            $user->teams()->attach($company->id, ['role' => 'owner']);
        }

        $this->banner("Empresa {$this->newCompanyName} criada!");
        $this->closeCreateModal();
    }

    public function openModal($id)
    {
        $this->selectedCompany = Company::with('users')->find($id);
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->selectedCompany = null;
    }

    public function sendInvitation()
    {
        $this->validate(['inviteEmail' => 'required|email']);

        $email = $this->inviteEmail;

        if ($this->selectedCompany->users()->where('email', $email)->exists()) {
            $this->addError('inviteEmail', 'Este usuário já está vinculado a esta empresa.');
            return;
        }

        if ($this->selectedCompany->invitations()->where('email', $email)->exists()) {
            $this->addError('inviteEmail', 'Já existe um convite pendente para este email.');
            return;
        }

        $invitation = TeamInvitation::create([
            'team_id' => $this->selectedCompany->id,
            'email' => $email,
            'role' => 'user',
        ]);

        $inviter = auth()->user();

        Notification::route('mail', $email)
            ->notify(new CompanyInvitation(
                $this->selectedCompany,
                $invitation,
                $inviter->name,
            ));

        $this->selectedCompany->load('users');
        $this->banner("Convite enviado para {$email}!");
        $this->inviteEmail = '';
    }

    public function removeUser($userId)
    {
        $user = User::find($userId);
        $this->selectedCompany->users()->detach($userId);
        $this->selectedCompany->load('users');

        if ($user && $user->teams()->count() === 0) {
            $user->system_id = null;
            $user->save();
        }

        $this->banner("{$user->name} removido da empresa!");
    }

    public function confirmDelete($id)
    {
        $company = Company::findOrFail($id);
        $this->confirmingId = $id;
        $this->confirmingMessage = "Excluir a empresa {$company->name}? Os usuários vinculados serão desassociados.";
    }

    public function executeAction()
    {
        $company = Company::findOrFail($this->confirmingId);
        $userIds = $company->users()->pluck('users.id');
        $company->users()->detach();
        $company->delete();

        User::whereIn('id', $userIds)->whereDoesntHave('teams')->whereNotNull('system_id')
            ->update(['system_id' => null]);
        $this->banner('Empresa excluída!');
        $this->confirmingId = null;
        $this->confirmingMessage = '';
    }

    public function cancelConfirmation()
    {
        $this->confirmingId = null;
        $this->confirmingMessage = '';
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedPerPage()
    {
        $this->resetPage();
    }

    public function render()
    {
        $user = auth()->user();
        $isSuperAdmin = $user->isSuperAdmin();

        if ($isSuperAdmin) {
            $query = Company::withCount('users')->with('owner');

            if ($this->search) {
                $query->where('name', 'like', "%{$this->search}%");
            }

            $companies = $query->orderBy('name')->paginate($this->perPage);
        } else {
            $query = Company::withCount('users')->with('owner')
                ->where('system_id', $user->system_id);

            if ($this->search) {
                $query->where('name', 'like', "%{$this->search}%");
            }

            $companies = $query->orderBy('name')->paginate($this->perPage);
        }

        return view('livewire.page.clientes.companies', [
            'companies' => $companies,
            'isSuperAdmin' => $isSuperAdmin,
        ])->layout('layouts.app-sidebar', [
            'showSidebar' => true,
            'title' => 'Empresas',
        ]);
    }
}
