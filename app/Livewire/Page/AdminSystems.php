<?php

namespace App\Livewire\Page;

use Livewire\Component;
use App\Models\System;
use Laravel\Jetstream\InteractsWithBanner;

class AdminSystems extends Component
{
    use InteractsWithBanner;

    public $showModal = false;
    public $systemId = null;
    public $slug = '';
    public $name = '';
    public $description = '';
    public $active = true;

    public $systems;

    protected function rules()
    {
        return [
            'slug' => ['required', 'string', 'max:30', 'unique:systems,slug,' . $this->systemId],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'active' => ['boolean'],
        ];
    }

    public function mount()
    {
        $this->loadSystems();
    }

    public function loadSystems()
    {
        $this->systems = System::orderBy('active', 'desc')->orderBy('name')->get();
    }

    public function openModal()
    {
        $this->authorize('delete');
        $this->resetForm();
        $this->showModal = true;
    }

    public function edit($id)
    {
        $this->authorize('delete');
        $system = System::findOrFail($id);
        $this->systemId = $system->id;
        $this->slug = $system->slug;
        $this->name = $system->name;
        $this->description = $system->description;
        $this->active = $system->active;
        $this->showModal = true;
    }

    public function save()
    {
        $this->authorize('delete');
        $this->validate();

        System::updateOrCreate(
            ['id' => $this->systemId],
            [
                'slug' => $this->slug,
                'name' => $this->name,
                'description' => $this->description,
                'active' => $this->active,
            ]
        );

        $this->banner($this->systemId ? 'Sistema atualizado com sucesso!' : 'Sistema criado com sucesso!');
        $this->showModal = false;
        $this->resetForm();
        $this->loadSystems();
    }

    public function resetForm()
    {
        $this->systemId = null;
        $this->slug = '';
        $this->name = '';
        $this->description = '';
        $this->active = true;
    }

    public function render()
    {
        return view('livewire.page.admin.systems')
            ->layout('layouts.app');
    }
}
