<?php

namespace App\Admin\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:160'],
            'slug' => ['nullable', 'string', 'max:160'],
            'description_ro' => ['nullable', 'string', 'max:5000'],
            'description_ru' => ['nullable', 'string', 'max:5000'],
            'details_ro' => ['nullable', 'string', 'max:5000'],
            'details_ru' => ['nullable', 'string', 'max:5000'],
            'is_active' => ['nullable', 'boolean'],
            'images' => ['nullable', 'array'],
            'images.*' => ['image', 'max:4096'],
            'keep_images' => ['nullable', 'array'],
            'keep_images.*' => ['integer'],
            'prices' => ['nullable', 'array'],
            'prices.*.days' => ['nullable', 'string', 'max:20'],
            'prices.*.price' => ['nullable', 'integer', 'min:0'],
            'prices.*.is_best' => ['nullable', 'boolean'],
            'addons' => ['nullable', 'array'],
            'addons.*.id' => ['nullable', 'integer'],
            'addons.*.title_ro' => ['nullable', 'string', 'max:160'],
            'addons.*.title_ru' => ['nullable', 'string', 'max:160'],
            'addons.*.price' => ['nullable', 'integer', 'min:0'],
            'addons.*.image' => ['nullable', 'image', 'max:4096'],
        ];
    }
}
