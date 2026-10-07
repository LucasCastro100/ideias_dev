<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MarketplaceLance extends Model
{
    protected $table = 'marketplace_bids';

    protected $fillable = [
        'user_id',
        'listing_id',
        'bidder_name',
        'amount',
        'bid_at',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'bid_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function listing()
    {
        return $this->belongsTo(MarketplaceAnuncio::class, 'listing_id');
    }
}
