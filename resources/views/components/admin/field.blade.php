@props(['label', 'name', 'type' => 'text', 'value' => null])

<div class="{{ $attributes->get('class') }}">
    <label class="block text-sm font-medium text-stone-700">
        {{ $label }}
        @if ($type === 'textarea')
            <textarea name="{{ $name }}" {{ $attributes->except('class')->merge(['rows' => 3, 'class' => 'admin-input mt-1.5 w-full']) }}>{{ $value }}</textarea>
        @elseif ($type === 'select')
            <select name="{{ $name }}" {{ $attributes->except('class')->merge(['class' => 'admin-input mt-1.5 w-full']) }}>{{ $slot }}</select>
        @elseif ($type === 'file')
            <input type="file" name="{{ $name }}" {{ $attributes->except('class')->merge(['class' => 'mt-1.5 block w-full text-sm font-normal']) }}>
        @else
            <input type="{{ $type }}" name="{{ $name }}" value="{{ $value }}" {{ $attributes->except('class')->merge(['class' => 'admin-input mt-1.5 w-full']) }}>
        @endif
    </label>
    @error($name)
        <p class="mt-1 text-sm text-red-700">{{ $message }}</p>
    @enderror
</div>
