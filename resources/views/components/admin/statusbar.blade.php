@props(['action', 'current'])

<form method="POST" action="{{ $action }}" {{ $attributes->merge(['class' => 'admin-track flex-wrap']) }}>
    @csrf
    @method('PATCH')
    @foreach (['new', 'confirmed', 'done'] as $step)
        <button type="submit" name="status" value="{{ $step }}" @class(['rounded-full px-3 py-1.5 text-sm font-medium', 'bg-emerald-800 text-white' => $current === $step, 'text-stone-600 hover:text-stone-950' => $current !== $step])>{{ __("admin.status_$step") }}</button>
    @endforeach
    <span class="mx-1 h-4 w-px bg-stone-300"></span>
    <button type="submit" name="status" value="cancelled" @class(['rounded-full px-3 py-1.5 text-sm font-medium', 'bg-red-700 text-white' => $current === 'cancelled', 'text-red-700 hover:text-red-800' => $current !== 'cancelled'])>{{ __('admin.status_cancelled') }}</button>
</form>
