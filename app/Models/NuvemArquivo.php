<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NuvemArquivo extends Model
{
    protected $table = 'nuvem_files';

    protected $fillable = [
        'user_id',
        'folder_id',
        'client_id',
        'name',
        'description',
        'size_kb',
        'is_public',
        'downloads_count',
        'share_token',
    ];

    protected function casts(): array
    {
        return [
            'is_public' => 'boolean',
            'downloads_count' => 'integer',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function folder()
    {
        return $this->belongsTo(NuvemPasta::class, 'folder_id');
    }

    public function client()
    {
        return $this->belongsTo(ArquivosCliente::class, 'client_id');
    }
}
