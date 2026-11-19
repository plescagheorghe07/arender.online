<?php

namespace App\Promos\Services;

use App\Promos\Models\PromoCode;

class PromoService
{
    public function award(string $code): PromoCode
    {
        return PromoCode::query()->updateOrCreate(
            ['code' => $code],
            [
                'label' => __('game.game_win_hint2'),
                'is_active' => true,
                'for_game' => true,
                'expires_at' => now()->addDays(7),
            ],
        );
    }
}
