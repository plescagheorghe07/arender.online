<?php

namespace App\Admin\Requests;

use Illuminate\Foundation\Http\FormRequest;

class NewsRequest extends FormRequest
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
            'title_ro' => ['required', 'string', 'max:200'],
            'title_ru' => ['required', 'string', 'max:200'],
            'body_ro' => ['required', 'string', 'max:20000'],
            'body_ru' => ['required', 'string', 'max:20000'],
            'slug' => ['nullable', 'string', 'max:160'],
            'published_on' => ['required', 'date'],
            'is_published' => ['nullable', 'boolean'],
            'images' => ['nullable', 'array'],
            'images.*' => ['image', 'max:4096'],
            'keep_images' => ['nullable', 'array'],
            'keep_images.*' => ['integer'],
        ];
    }
}
