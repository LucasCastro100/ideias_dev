<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailMktAssinante extends Model
{
    protected $table = 'email_subscribers';

    protected $fillable = [
        'user_id',
        'list_id',
        'email',
        'name',
        'confirmed',
        'unsubscribed',
    ];

    protected function casts(): array
    {
        return [
            'confirmed' => 'boolean',
            'unsubscribed' => 'boolean',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function lista()
    {
        return $this->belongsTo(EmailMktLista::class, 'list_id');
    }
}
