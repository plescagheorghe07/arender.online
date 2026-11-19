<div class="admin-row grid items-end gap-3 sm:grid-cols-[1fr_1fr_auto_auto]" data-value-row>
    <label class="text-sm">{{ __('admin.days') }}
        <input name="prices[{{ $index }}][days]" value="{{ $row['days'] ?? '' }}" class="admin-input mt-1 w-full">
    </label>
    <label class="text-sm">{{ __('admin.price') }}
        <input name="prices[{{ $index }}][price]" type="number" value="{{ $row['price'] ?? '' }}" class="admin-input mt-1 w-full">
    </label>
    <label class="flex items-center gap-2 pb-2 text-sm">
        <input type="checkbox" name="prices[{{ $index }}][is_best]" value="1" @checked(! empty($row['is_best']))>
        {{ __('admin.best') }}
    </label>
    <button type="button" data-remove-row class="mb-0.5 grid h-9 w-9 cursor-pointer place-items-center rounded-full text-red-700 hover:bg-red-50" aria-label="{{ __('admin.delete') }}">
        <x-admin.icon name="trash" class="h-4 w-4" />
    </button>
</div>
