<?php

namespace App\Livewire\Page;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\ClinicaPaciente;
use Laravel\Jetstream\InteractsWithBanner;

class ClinicaPacientes extends Component
{
    use InteractsWithBanner, WithPagination;

    public $showModal = false;
    public $patientId = null;
    public $name = '';
    public $phone = '';
    public $birth_date = '';
    public $perPage = 10;

    public $confirmingId = null;
    public $confirmingMessage = '';

    protected function rules()
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'birth_date' => ['nullable', 'date'],
        ];
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
        $patient = ClinicaPaciente::where('user_id', auth()->id())->findOrFail($id);
        $this->patientId = $patient->id;
        $this->name = $patient->name;
        $this->phone = $patient->phone;
        $this->birth_date = $patient->birth_date?->format('Y-m-d');
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate();

        ClinicaPaciente::updateOrCreate(
            ['id' => $this->patientId],
            [
                'user_id' => $this->patientId ? ClinicaPaciente::find($this->patientId)->user_id : auth()->id(),
                'name' => $this->name,
                'phone' => $this->phone ?: null,
                'birth_date' => $this->birth_date ?: null,
            ]
        );

        $this->banner($this->patientId ? 'Paciente atualizado com sucesso!' : 'Paciente cadastrado com sucesso!');
        $this->showModal = false;
        $this->resetForm();
    }

    public function confirmDelete($id)
    {
        $this->confirmingId = $id;
        $this->confirmingMessage = 'Excluir este paciente? Esta ação não pode ser desfeita.';
    }

    public function executeAction()
    {
        ClinicaPaciente::where('user_id', auth()->id())->findOrFail($this->confirmingId)->delete();
        $this->banner('Paciente excluído com sucesso!');
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
        $this->patientId = null;
        $this->name = '';
        $this->phone = '';
        $this->birth_date = '';
    }

    public function render()
    {
        $query = ClinicaPaciente::where('user_id', auth()->id());

        return view('livewire.page.sistema-clinica.pacientes', [
            'patients' => $query->orderBy('name')->paginate($this->perPage),
        ])->layout('layouts.app-sidebar', [
            'showSidebar' => true,
            'title' => 'Pacientes',
        ]);
    }
}
