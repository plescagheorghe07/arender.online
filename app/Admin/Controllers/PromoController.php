<?php

namespace App\Admin\Controllers;

use App\Admin\Requests\PromoRequest;
use App\Http\Controllers\Controller;
use App\Promos\Models\PromoCode;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PromoController extends Controller
{
    public function index(): View
    {
        return view('admin.promos.index', [
            'codes' => PromoCode::query()->latest()->get(),
        ]);
    }

    public function store(PromoRequest $request): RedirectResponse
    {
        PromoCode::query()->create([
            'code' => strtoupper($request->string('code')->toString()),
            'label' => $request->string('label')->toString(),
            'is_active' => $request->boolean('is_active'),
            'for_game' => $request->boolean('for_game'),
        ]);

        return back()->with('status', __('admin.saved'));
    }

    public function update(Request $request, PromoCode $promo): RedirectResponse
    {
        $data = $request->validate([
            'code' => ['required', 'string', 'max:40', Rule::unique('promo_codes', 'code')->ignore($promo->id)],
            'label' => ['required', 'string', 'max:160'],
        ]);

        $promo->update([
            'code' => strtoupper($data['code']),
            'label' => $data['label'],
            'is_active' => $request->boolean('is_active'),
            'for_game' => $request->boolean('for_game'),
        ]);

        return back()->with('status', __('admin.saved'));
    }

    public function destroy(PromoCode $promo): RedirectResponse
    {
        $promo->delete();

        return back()->with('status', __('admin.deleted'));
    }
}
