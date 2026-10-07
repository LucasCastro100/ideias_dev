<?php

namespace App\Livewire\Page;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\CmsPagina;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use Laravel\Jetstream\InteractsWithBanner;

class CmsPaginas extends Component
{
    use InteractsWithBanner, WithPagination;

    public $showModal = false;
    public $paginaId = null;
    public $title = '';
    public $slug = '';
    public $content = '';
    public $published = true;
    public $slugManuallyEdited = false;

    public $search = '';
    public $perPage = 10;

    public $confirmingId = null;
    public $confirmingMessage = '';

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedPerPage()
    {
        $this->resetPage();
    }

    public function updatedTitle($value)
    {
        if (!$this->slugManuallyEdited) {
            $this->slug = Str::slug($value);
        }
    }

    public function updatedSlug($value)
    {
        $this->slugManuallyEdited = true;
        $this->slug = Str::slug($value);
    }

    public function openModal()
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function edit($id)
    {
        $pagina = CmsPagina::where('user_id', auth()->id())->findOrFail($id);
        $this->paginaId = $pagina->id;
        $this->title = $pagina->title;
        $this->slug = $pagina->slug;
        $this->content = $pagina->content;
        $this->published = $pagina->published;
        $this->slugManuallyEdited = true;
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate([
            'title' => 'required|string|max:255',
            'slug' => [
                'required',
                'string',
                'max:255',
                Rule::unique('cms_pages', 'slug')
                    ->where('user_id', auth()->id())
                    ->ignore($this->paginaId),
            ],
            'content' => 'nullable|string',
            'published' => 'boolean',
        ]);

        CmsPagina::updateOrCreate(
            ['id' => $this->paginaId],
            [
                'user_id' => $this->paginaId ? CmsPagina::find($this->paginaId)->user_id : auth()->id(),
                'title' => $this->title,
                'slug' => $this->slug,
                'content' => $this->content ?: null,
                'published' => $this->published,
            ]
        );

        $this->banner($this->paginaId ? 'Página atualizada com sucesso!' : 'Página cadastrada com sucesso!');
        $this->showModal = false;
        $this->resetForm();
    }

    public function confirmDelete($id)
    {
        $this->confirmingId = $id;
        $this->confirmingMessage = 'Excluir esta página? Esta ação não pode ser desfeita.';
    }

    public function executeAction()
    {
        CmsPagina::where('user_id', auth()->id())->findOrFail($this->confirmingId)->delete();
        $this->banner('Página excluída com sucesso!');
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
        $this->paginaId = null;
        $this->title = '';
        $this->slug = '';
        $this->content = '';
        $this->published = true;
        $this->slugManuallyEdited = false;
    }

    public function render()
    {
        $query = CmsPagina::where('user_id', auth()->id());

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('title', 'like', "%{$this->search}%")
                  ->orWhere('slug', 'like', "%{$this->search}%");
            });
        }

        return view('livewire.page.site-institucional-cms.paginas', [
            'paginas' => $query->orderBy('title')->paginate($this->perPage),
        ])->layout('layouts.app-sidebar', [
            'showSidebar' => true,
            'title' => 'Páginas',
        ]);
    }
}
