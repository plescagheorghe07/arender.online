@props(['edit' => null, 'delete' => null, 'open' => null])

<div class="relative flex justify-end" data-menu>
    <button type="button" data-menu-button class="cursor-pointer rounded-full p-2 text-stone-500 hover:bg-stone-50" aria-label="{{ __('admin.actions') }}">
        <x-admin.icon name="dots" />
    </button>
    <div data-menu-panel class="hidden w-56 overflow-hidden rounded-2xl bg-white py-1 text-sm shadow-[0_16px_40px_rgba(28,25,23,0.12)]" style="position:fixed;z-index:60">
        @if ($open)
            <a href="{{ $open }}" class="block px-3 py-2 hover:bg-stone-50">{{ __('admin.open_order') }}</a>
        @endif
        @if ($edit)
            @if (\Illuminate\Support\Str::startsWith($edit, '#'))
                <button type="button" data-open-dialog="{{ \Illuminate\Support\Str::after($edit, '#') }}" class="block w-full cursor-pointer px-3 py-2 text-left hover:bg-stone-50">{{ __('admin.edit') }}</button>
            @else
                <a href="{{ $edit }}" class="block px-3 py-2 hover:bg-stone-50">{{ __('admin.edit') }}</a>
            @endif
        @endif
        @if ($delete)
            <form method="POST" action="{{ $delete }}" data-confirm="{{ __('admin.delete_confirm') }}">
                @csrf
                @method('DELETE')
                <button type="submit" class="block w-full cursor-pointer px-3 py-2 text-left text-red-700 hover:bg-stone-50">{{ __('admin.delete') }}</button>
            </form>
        @endif
    </div>
</div>
