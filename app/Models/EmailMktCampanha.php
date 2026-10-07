<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailMktCampanha extends Model
{
    protected $table = 'email_campaigns';

    protected $fillable = [
        'user_id',
        'list_id',
        'subject',
        'body',
        'status',
        'sent_at',
    ];

    protected function casts(): array
    {
        return [
            'sent_at' => 'datetime',
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
