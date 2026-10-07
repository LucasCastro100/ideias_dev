<?php

namespace App\Livewire\Page;

use Livewire\Component;
use App\Models\Project;

class PublicProjectDetail extends Component
{
    public Project $project;

    public function mount($slug)
    {
        $this->project = Project::where('slug', $slug)->where('active', true)->firstOrFail();
    }

    public function render()
    {
        $accessRouteName = $this->project->system_slug . '.dashboard';
        $hasAccessRoute = \Illuminate\Support\Facades\Route::has($accessRouteName);

        $related = Project::where('active', true)
            ->where('category', $this->project->category)
            ->where('id', '!=', $this->project->id)
            ->orderBy('sort_order')
            ->limit(3)
            ->get();

        return view('livewire.page.public.project-detail', [
            'accessRouteName' => $hasAccessRoute ? $accessRouteName : null,
            'related' => $related,
        ])->layout('layouts.app-tbr-public', [
            'title' => $this->project->name . ' | Ideias Dev',
        ]);
    }
}
