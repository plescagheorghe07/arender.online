<a href="{{ route('catalog.show', $product->slug) }}" class="group flex h-full flex-col overflow-hidden rounded-[1.75rem] bg-white shadow-[0_12px_32px_rgba(28,25,23,0.06)] transition duration-200 hover:-translate-y-1 hover:shadow-[0_18px_40px_rgba(6,78,59,0.12)]">
    <div class="flex aspect-[4/3] items-center justify-center bg-gradient-to-br from-sky-100 via-white to-violet-100">
        @if ($product->imageUrl)
            <img src="{{ $product->imageUrl }}" alt="{{ $product->name }}" class="h-full w-full object-contain p-4 transition duration-300 group-hover:scale-[1.04]">
        @endif
    </div>
    <div class="flex flex-1 flex-col p-5">
        <h2 class="text-lg font-semibold tracking-tight">{{ $product->name }}</h2>
        @if ($product->summary !== '')
            <p class="mt-2 line-clamp-3 flex-1 text-sm leading-6 text-stone-600">{{ $product->summary }}</p>
        @endif
        @if ($product->fromPrice)
            <p class="mt-4 inline-flex items-center gap-1.5 self-start rounded-full bg-gradient-to-r from-sky-50 to-violet-50 px-3 py-1 text-sm font-medium text-blue-700">
                <x-site.icon name="spark" class="h-3.5 w-3.5" />
                {{ __('ui.from_price', ['price' => number_format($product->fromPrice, 0, ',', ' ')]) }}
            </p>
        @endif
    </div>
</a>
