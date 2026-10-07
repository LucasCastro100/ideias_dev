<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArquivosCliente extends Model
{
    protected $table = 'arquivos_clients';

    protected $fillable = [
        'user_id',
        'name',
        'email',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function files()
    {
        return $this->hasMany(NuvemArquivo::class, 'client_id');
    }
}
