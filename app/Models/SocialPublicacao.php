<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SocialPublicacao extends Model
{
    protected $table = 'social_posts';

    protected $fillable = [
        'user_id',
        'content',
        'media_url',
        'visibility',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
