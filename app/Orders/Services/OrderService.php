<?php

namespace App\Orders\Services;

use App\Catalog\Models\Product;
use App\Orders\Models\Order;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class OrderService
{
    public function __construct(private readonly OrderNotifier $mail) {}

    /**
     * @param  array<string, mixed>  $input
     */
    public function place(Product $product, array $input): Order
    {
        $data = Validator::make($input, [
            'name' => ['required', 'string', 'max:120'],
            'phone' => ['required', 'string', 'max:40'],
            'email' => ['required', 'email', 'max:160'],
            'address' => ['required', 'string', 'max:255'],
            'days' => ['required', 'string', 'max:20'],
            'note' => ['nullable', 'string', 'max:1000'],
            'promo_code' => ['nullable', 'string', 'max:40'],
            'addons' => ['nullable', 'array'],
            'addons.*' => ['integer'],
            'privacy' => ['accepted'],
        ], [
            'privacy.accepted' => __('ui.order_privacy_required'),
        ])->validate();

        $price = $product->prices->firstWhere('days_label', $data['days']);

        if ($price === null) {
            throw ValidationException::withMessages([
                'days' => __('ui.order_days_invalid'),
            ]);
        }

        $selected = $product->addons
            ->whereIn('id', $data['addons'] ?? [])
            ->values();

        $locale = app()->getLocale();

        $order = Order::query()->create([
            'product_id' => $product->id,
            'product_name' => $product->name,
            'days_label' => $price->days_label,
            'price' => $price->price + $selected->sum('price'),
            'addons' => $selected->map(fn ($addon): array => [
                'title' => $locale === 'ru' ? $addon->title_ru : $addon->title_ro,
                'price' => $addon->price,
            ])->all(),
            'customer_name' => $data['name'],
            'phone' => $data['phone'],
            'email' => $data['email'],
            'locale' => $locale,
            'address' => $data['address'],
            'note' => $data['note'] ?? null,
            'promo_code' => $data['promo_code'] ?? null,
            'status' => 'new',
            'privacy_accepted_at' => now(),
        ]);

        $this->mail->created($order);

        return $order;
    }
}
