<?php

namespace App\Promos\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['code', 'label', 'is_active', 'for_game', 'expires_at'])]
class PromoCode extends Model
{
    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'for_game' => 'boolean',
            'expires_at' => 'datetime',
        ];
    }
}
