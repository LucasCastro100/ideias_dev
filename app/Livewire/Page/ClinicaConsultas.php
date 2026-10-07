<?php

namespace App\Livewire\Page;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\ClinicaConsulta;
use App\Models\ClinicaPaciente;
use Laravel\Jetstream\InteractsWithBanner;

class ClinicaConsultas extends Component
{
    use InteractsWithBanner, WithPagination;

    public $showModal = false;
    public $appointmentId = null;
    public $patient_id = '';
    public $doctor_name = '';
    public $appointment_at = '';
    public $status = 'agendada';
    public $perPage = 10;

    public $patients;

    public $confirmingId = null;
    public $confirmingMessage = '';

    protected function rules()
    {
        return [
            'patient_id' => ['required', 'exists:clinica_patients,id'],
            'doctor_name' => ['nullable', 'string', 'max:255'],
            'appointment_at' => ['required', 'date'],
            'status' => ['required', 'in:agendada,realizada,cancelada'],
        ];
    }

    public function mount()
    {
        $this->patients = ClinicaPaciente::where('user_id', auth()->id())->orderBy('name')->get();
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
        $appointment = ClinicaConsulta::where('user_id', auth()->id())->findOrFail($id);
        $this->appointmentId = $appointment->id;
        $this->patient_id = $appointment->patient_id;
        $this->doctor_name = $appointment->doctor_name;
        $this->appointment_at = $appointment->appointment_at?->format('Y-m-d\TH:i');
        $this->status = $appointment->status;
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate();

        ClinicaConsulta::updateOrCreate(
            ['id' => $this->appointmentId],
            [
                'user_id' => $this->appointmentId ? ClinicaConsulta::find($this->appointmentId)->user_id : auth()->id(),
                'patient_id' => $this->patient_id,
                'doctor_name' => $this->doctor_name ?: null,
                'appointment_at' => $this->appointment_at,
                'status' => $this->status,
            ]
        );

        $this->banner($this->appointmentId ? 'Consulta atualizada com sucesso!' : 'Consulta cadastrada com sucesso!');
        $this->showModal = false;
        $this->resetForm();
    }

    public function confirmDelete($id)
    {
        $this->confirmingId = $id;
        $this->confirmingMessage = 'Excluir esta consulta? Esta ação não pode ser desfeita.';
    }

    public function executeAction()
    {
        ClinicaConsulta::where('user_id', auth()->id())->findOrFail($this->confirmingId)->delete();
        $this->banner('Consulta excluída com sucesso!');
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
        $this->appointmentId = null;
        $this->patient_id = '';
        $this->doctor_name = '';
        $this->appointment_at = '';
        $this->status = 'agendada';
    }

    public function render()
    {
        $query = ClinicaConsulta::where('user_id', auth()->id())->with('patient');

        return view('livewire.page.sistema-clinica.consultas', [
            'appointments' => $query->orderBy('appointment_at', 'desc')->paginate($this->perPage),
            'patients' => $this->patients,
        ])->layout('layouts.app-sidebar', [
            'showSidebar' => true,
            'title' => 'Consultas',
        ]);
    }
}
