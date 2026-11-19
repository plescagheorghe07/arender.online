@props(['id', 'title', 'open' => false, 'wide' => false])

<div id="{{ $id }}" data-dialog @if ($open) data-open @endif class="fixed top-0 right-0 bottom-0 left-0 z-40 hidden items-center justify-center p-4" style="background:rgba(28,25,23,.45)" role="dialog" aria-modal="true" aria-labelledby="{{ $id }}-title">
    <div @class(['max-h-screen w-full overflow-y-auto rounded-3xl bg-white p-5 shadow-xl', 'max-w-2xl' => $wide, 'max-w-xl' => ! $wide]) style="max-height:90vh">
        <div class="flex items-start justify-between gap-3">
            <h2 id="{{ $id }}-title" class="text-lg font-semibold tracking-tight">{{ $title }}</h2>
            <button type="button" data-close-dialog class="cursor-pointer rounded-full px-2 py-1 text-lg leading-none text-stone-500 hover:bg-stone-50" aria-label="{{ __('admin.cancel') }}">×</button>
        </div>
        <div class="mt-4">{{ $slot }}</div>
    </div>
</div>
