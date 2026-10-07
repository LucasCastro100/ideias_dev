<?php

namespace App\Livewire\Page;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\SocialPublicacao;
use Laravel\Jetstream\InteractsWithBanner;

class SocialPublicacoes extends Component
{
    use InteractsWithBanner, WithPagination;

    public $showModal = false;
    public $postId = null;
    public $content = '';
    public $media_url = '';
    public $visibility = 'publico';
    public $perPage = 10;

    public $confirmingId = null;
    public $confirmingMessage = '';

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
        $post = SocialPublicacao::where('user_id', auth()->id())->findOrFail($id);
        $this->postId = $post->id;
        $this->content = $post->content;
        $this->media_url = $post->media_url;
        $this->visibility = $post->visibility;
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate([
            'content' => 'required|string',
            'media_url' => 'nullable|string|max:255',
            'visibility' => 'required|in:publico,amigos,privado',
        ]);

        SocialPublicacao::updateOrCreate(
            ['id' => $this->postId],
            [
                'user_id' => $this->postId ? SocialPublicacao::find($this->postId)->user_id : auth()->id(),
                'content' => $this->content,
                'media_url' => $this->media_url ?: null,
                'visibility' => $this->visibility,
            ]
        );

        $this->banner($this->postId ? 'Publicação atualizada com sucesso!' : 'Publicação criada com sucesso!');
        $this->showModal = false;
        $this->resetForm();
    }

    public function confirmDelete($id)
    {
        $this->confirmingId = $id;
        $this->confirmingMessage = 'Excluir esta publicação? Esta ação não pode ser desfeita.';
    }

    public function executeAction()
    {
        SocialPublicacao::where('user_id', auth()->id())->findOrFail($this->confirmingId)->delete();
        $this->banner('Publicação excluída com sucesso!');
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
        $this->postId = null;
        $this->content = '';
        $this->media_url = '';
        $this->visibility = 'publico';
    }

    public function render()
    {
        $posts = SocialPublicacao::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage);

        return view('livewire.page.rede-social.publicacoes', [
            'posts' => $posts,
        ])->layout('layouts.app-sidebar', [
            'showSidebar' => true,
            'title' => 'Publicações',
        ]);
    }
}
