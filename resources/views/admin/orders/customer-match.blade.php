<li class="rounded-2xl bg-stone-50 px-4 py-3 text-sm">
    <p class="font-medium">{{ $customer->name }}</p>
    @if ($customer->phones->isNotEmpty())
        <p class="mt-1 text-stone-600">{{ $customer->phones->pluck('phone')->join(' · ') }}</p>
    @endif
    @if ($customer->emails->isNotEmpty())
        <p class="text-stone-600">{{ $customer->emails->pluck('email')->join(' · ') }}</p>
    @endif
    @if ($customer->addresses->isNotEmpty())
        <p class="text-stone-500">{{ $customer->addresses->pluck('address')->join(' · ') }}</p>
    @endif
    @if ($order->customer_id === $customer->id)
        <p class="mt-2 font-medium text-emerald-900">{{ __('admin.attached') }}</p>
    @else
        <form method="POST" action="{{ route('admin.orders.customer.attach', $order) }}" class="mt-2">
            @csrf
            <input type="hidden" name="customer_id" value="{{ $customer->id }}">
            <button type="submit" class="cursor-pointer rounded-xl bg-emerald-800 px-3 py-1.5 text-sm font-medium text-white hover:bg-emerald-900">{{ __('admin.attach') }}</button>
        </form>
    @endif
</li>
