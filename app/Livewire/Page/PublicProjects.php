<?php

namespace App\Livewire\Page;

use Livewire\Component;
use App\Models\Project;

class PublicProjects extends Component
{
    public $search = '';
    public $category = '';

    public function render()
    {
        $query = Project::where('active', true);

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                    ->orWhere('short_description', 'like', "%{$this->search}%");
            });
        }

        if ($this->category) {
            $query->where('category', $this->category);
        }

        $projects = $query->orderBy('sort_order')->get();

        $categories = Project::where('active', true)
            ->whereNotNull('category')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');

        return view('livewire.page.public.projects', [
            'projects' => $projects,
            'categories' => $categories,
        ])->layout('layouts.app-tbr-public', [
            'title' => 'Projetos | Ideias Dev',
        ]);
    }
}
