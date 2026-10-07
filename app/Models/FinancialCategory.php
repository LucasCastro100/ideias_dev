<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FinancialCategory extends Model
{
    protected $fillable = [
        'name',
        'type',
        'description',
        'color',
    ];

    public function transactions()
    {
        return $this->hasMany(FinancialTransaction::class, 'category_id');
    }
}
