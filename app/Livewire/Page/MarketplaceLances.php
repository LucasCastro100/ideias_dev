<?php

namespace App\Livewire\Page;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\MarketplaceAnuncio;
use App\Models\MarketplaceLance;
use Laravel\Jetstream\InteractsWithBanner;

class MarketplaceLances extends Component
{
    use InteractsWithBanner, WithPagination;

    public $showModal = false;
    public $lanceId = null;
    public $listing_id = '';
    public $bidder_name = '';
    public $amount = '';
    public $bid_at = '';
    public $perPage = 10;

    public $listings;

    public $confirmingId = null;
    public $confirmingMessage = '';

    public function mount()
    {
        $this->listings = MarketplaceAnuncio::where('user_id', auth()->id())->orderBy('title')->get();
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
        $lance = MarketplaceLance::where('user_id', auth()->id())->findOrFail($id);
        $this->lanceId = $lance->id;
        $this->listing_id = $lance->listing_id;
        $this->bidder_name = $lance->bidder_name;
        $this->amount = $lance->amount;
        $this->bid_at = $lance->bid_at?->format('Y-m-d\TH:i');
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate([
            'listing_id' => 'required|exists:marketplace_listings,id',
            'bidder_name' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'bid_at' => 'nullable|date',
        ]);

        MarketplaceLance::updateOrCreate(
            ['id' => $this->lanceId],
            [
                'user_id' => $this->lanceId ? MarketplaceLance::find($this->lanceId)->user_id : auth()->id(),
                'listing_id' => $this->listing_id,
                'bidder_name' => $this->bidder_name,
                'amount' => $this->amount,
                'bid_at' => $this->bid_at ?: null,
            ]
        );

        $this->banner($this->lanceId ? 'Lance atualizado com sucesso!' : 'Lance cadastrado com sucesso!');
        $this->showModal = false;
        $this->resetForm();
    }

    public function confirmDelete($id)
    {
        $this->confirmingId = $id;
        $this->confirmingMessage = 'Excluir este lance? Esta ação não pode ser desfeita.';
    }

    public function executeAction()
    {
        MarketplaceLance::where('user_id', auth()->id())->findOrFail($this->confirmingId)->delete();
        $this->banner('Lance excluído com sucesso!');
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
        $this->lanceId = null;
        $this->listing_id = '';
        $this->bidder_name = '';
        $this->amount = '';
        $this->bid_at = '';
    }

    public function render()
    {
        $lances = MarketplaceLance::where('user_id', auth()->id())
            ->with('listing')
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage);

        return view('livewire.page.marketplace-leiloes.lances', [
            'lances' => $lances,
        ])->layout('layouts.app-sidebar', [
            'showSidebar' => true,
            'title' => 'Lances',
        ]);
    }
}
