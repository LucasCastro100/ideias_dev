<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MarketplaceAnuncio extends Model
{
    protected $table = 'marketplace_listings';

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'listing_type',
        'price',
        'current_bid',
        'status',
        'ends_at',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'current_bid' => 'decimal:2',
            'ends_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function bids()
    {
        return $this->hasMany(MarketplaceLance::class, 'listing_id');
    }
}
