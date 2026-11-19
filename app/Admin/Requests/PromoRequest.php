<?php

namespace App\Admin\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PromoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->managesCatalog() === true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'code' => ['required', 'string', 'max:40', Rule::unique('promo_codes', 'code')],
            'label' => ['required', 'string', 'max:160'],
            'is_active' => ['nullable', 'boolean'],
            'for_game' => ['nullable', 'boolean'],
        ];
    }
}
