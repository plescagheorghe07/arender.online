<?php

namespace App\Promos\Controllers;

use App\Http\Controllers\Controller;
use App\Promos\Services\PromoService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MinigameController extends Controller
{
    public function __construct(
        private readonly PromoService $promos,
    ) {}

    public function show(string $locale): View
    {
        return view('pages.minigame');
    }

    public function reward(Request $request, string $locale): JsonResponse
    {
        $data = $request->validate([
            'code' => ['required', 'string', 'size:4', 'regex:/^[A-HJ-NP-Z2-9]{4}$/'],
        ]);

        $reward = $this->promos->award($data['code']);

        return response()->json([
            'code' => $reward->code,
            'label' => $reward->label,
        ]);
    }
}
