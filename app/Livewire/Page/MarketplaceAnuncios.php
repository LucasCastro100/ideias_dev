<?php

namespace App\Livewire\Page;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\MarketplaceAnuncio;
use Laravel\Jetstream\InteractsWithBanner;

class MarketplaceAnuncios extends Component
{
    use InteractsWithBanner, WithPagination;

    public $showModal = false;
    public $anuncioId = null;
    public $title = '';
    public $description = '';
    public $listing_type = 'fixo';
    public $price = '';
    public $current_bid = '';
    public $status = 'ativo';
    public $ends_at = '';
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
        $anuncio = MarketplaceAnuncio::where('user_id', auth()->id())->findOrFail($id);
        $this->anuncioId = $anuncio->id;
        $this->title = $anuncio->title;
        $this->description = $anuncio->description;
        $this->listing_type = $anuncio->listing_type;
        $this->price = $anuncio->price;
        $this->current_bid = $anuncio->current_bid;
        $this->status = $anuncio->status;
        $this->ends_at = $anuncio->ends_at?->format('Y-m-d\TH:i');
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'listing_type' => 'required|in:fixo,leilao',
            'price' => 'nullable|numeric|min:0',
            'current_bid' => 'nullable|numeric|min:0',
            'status' => 'required|in:ativo,encerrado,cancelado',
            'ends_at' => 'nullable|date',
        ]);

        MarketplaceAnuncio::updateOrCreate(
            ['id' => $this->anuncioId],
            [
                'user_id' => $this->anuncioId ? MarketplaceAnuncio::find($this->anuncioId)->user_id : auth()->id(),
                'title' => $this->title,
                'description' => $this->description ?: null,
                'listing_type' => $this->listing_type,
                'price' => $this->price !== '' ? $this->price : null,
                'current_bid' => $this->current_bid !== '' ? $this->current_bid : null,
                'status' => $this->status,
                'ends_at' => $this->ends_at ?: null,
            ]
        );

        $this->banner($this->anuncioId ? 'Anúncio atualizado com sucesso!' : 'Anúncio cadastrado com sucesso!');
        $this->showModal = false;
        $this->resetForm();
    }

    public function confirmDelete($id)
    {
        $this->confirmingId = $id;
        $this->confirmingMessage = 'Excluir este anúncio? Esta ação não pode ser desfeita.';
    }

    public function executeAction()
    {
        MarketplaceAnuncio::where('user_id', auth()->id())->findOrFail($this->confirmingId)->delete();
        $this->banner('Anúncio excluído com sucesso!');
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
        $this->anuncioId = null;
        $this->title = '';
        $this->description = '';
        $this->listing_type = 'fixo';
        $this->price = '';
        $this->current_bid = '';
        $this->status = 'ativo';
        $this->ends_at = '';
    }

    public function render()
    {
        $anuncios = MarketplaceAnuncio::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage);

        return view('livewire.page.marketplace-leiloes.anuncios', [
            'anuncios' => $anuncios,
        ])->layout('layouts.app-sidebar', [
            'showSidebar' => true,
            'title' => 'Anúncios',
        ]);
    }
}
