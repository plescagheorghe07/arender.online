@props(['action', 'value' => '', 'placeholder' => null])

<form method="GET" action="{{ $action }}" {{ $attributes->merge(['class' => 'flex flex-1 gap-2']) }}>
    @foreach (request()->except('q') as $key => $kept)
        @if (is_string($kept) && $kept !== '')
            <input type="hidden" name="{{ $key }}" value="{{ $kept }}">
        @endif
    @endforeach
    <input name="q" value="{{ $value }}" placeholder="{{ $placeholder ?? __('admin.search') }}" class="admin-input min-w-0 flex-1">
    <x-admin.button variant="ghost">{{ __('admin.search') }}</x-admin.button>
</form>
