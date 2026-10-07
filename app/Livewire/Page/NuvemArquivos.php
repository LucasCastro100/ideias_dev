<?php

namespace App\Livewire\Page;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\NuvemArquivo;
use App\Models\NuvemPasta;
use App\Models\ArquivosCliente;
use Laravel\Jetstream\InteractsWithBanner;
use Illuminate\Support\Str;

class NuvemArquivos extends Component
{
    use InteractsWithBanner, WithPagination;

    public $showModal = false;
    public $fileId = null;
    public $folder_id = '';
    public $client_id = '';
    public $name = '';
    public $description = '';
    public $size_kb = '';
    public $is_public = false;
    public $downloads_count = 0;
    public $search = '';
    public $perPage = 10;

    public $folders;
    public $clientes;

    public $confirmingId = null;
    public $confirmingMessage = '';

    public function mount()
    {
        $this->folders = NuvemPasta::where('user_id', auth()->id())->orderBy('name')->get();
        $this->clientes = ArquivosCliente::where('user_id', auth()->id())->orderBy('name')->get();
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
        $file = NuvemArquivo::where('user_id', auth()->id())->findOrFail($id);
        $this->fileId = $file->id;
        $this->folder_id = $file->folder_id;
        $this->client_id = $file->client_id;
        $this->name = $file->name;
        $this->description = $file->description;
        $this->size_kb = $file->size_kb;
        $this->is_public = $file->is_public;
        $this->downloads_count = $file->downloads_count;
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'folder_id' => 'nullable|exists:nuvem_folders,id',
            'client_id' => 'nullable|exists:arquivos_clients,id',
            'description' => 'nullable|string',
            'size_kb' => 'nullable|integer|min:0',
            'is_public' => 'boolean',
            'downloads_count' => 'nullable|integer|min:0',
        ]);

        $file = NuvemArquivo::updateOrCreate(
            ['id' => $this->fileId],
            [
                'user_id' => $this->fileId ? NuvemArquivo::find($this->fileId)->user_id : auth()->id(),
                'folder_id' => $this->folder_id ?: null,
                'client_id' => $this->client_id ?: null,
                'name' => $this->name,
                'description' => $this->description ?: null,
                'size_kb' => $this->size_kb !== '' ? $this->size_kb : null,
                'is_public' => $this->is_public,
                'downloads_count' => $this->downloads_count !== '' ? $this->downloads_count : 0,
            ]
        );

        if ($this->is_public && !$file->share_token) {
            $file->update(['share_token' => Str::random(32)]);
        }

        $this->banner($this->fileId ? 'Arquivo atualizado com sucesso!' : 'Arquivo cadastrado com sucesso!');
        $this->showModal = false;
        $this->resetForm();
    }

    public function confirmDelete($id)
    {
        $this->confirmingId = $id;
        $this->confirmingMessage = 'Excluir este arquivo? Esta ação não pode ser desfeita.';
    }

    public function executeAction()
    {
        NuvemArquivo::where('user_id', auth()->id())->findOrFail($this->confirmingId)->delete();
        $this->banner('Arquivo excluído com sucesso!');
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
        $this->fileId = null;
        $this->folder_id = '';
        $this->client_id = '';
        $this->name = '';
        $this->description = '';
        $this->size_kb = '';
        $this->is_public = false;
        $this->downloads_count = 0;
    }

    public function render()
    {
        $query = NuvemArquivo::where('user_id', auth()->id());

        if ($this->search) {
            $query->where('name', 'like', "%{$this->search}%");
        }

        $files = $query->with(['folder', 'client'])
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage);

        return view('livewire.page.armazenamento-nuvem.arquivos', [
            'files' => $files,
        ])->layout('layouts.app-sidebar', [
            'showSidebar' => true,
            'title' => 'Arquivos',
        ]);
    }
}
