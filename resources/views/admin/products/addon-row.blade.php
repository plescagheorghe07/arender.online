<div class="admin-row" data-value-row>
    @if (! empty($row['id']))
        <input type="hidden" name="addons[{{ $index }}][id]" value="{{ $row['id'] }}">
    @endif
    <div class="flex items-start gap-3">
        <div class="min-w-0 flex-1">
            <x-admin.locale-tabs compact :initial="(filled($row['title_ru'] ?? null) && blank($row['title_ro'] ?? null)) ? 'ru' : 'ro'">
                <x-slot:ro>
                    <label class="block text-sm">{{ __('admin.title_ro') }}
                        <input name="addons[{{ $index }}][title_ro]" value="{{ $row['title_ro'] ?? '' }}" class="admin-input mt-1 w-full">
                    </label>
                </x-slot:ro>
                <x-slot:ru>
                    <label class="block text-sm">{{ __('admin.title_ru') }}
                        <input name="addons[{{ $index }}][title_ru]" value="{{ $row['title_ru'] ?? '' }}" class="admin-input mt-1 w-full">
                    </label>
                </x-slot:ru>
            </x-admin.locale-tabs>
        </div>
        <label class="w-28 shrink-0 text-sm">{{ __('admin.price') }}
            <input name="addons[{{ $index }}][price]" type="number" value="{{ $row['price'] ?? '' }}" class="admin-input mt-1 w-full">
        </label>
        <button type="button" data-remove-row class="mt-6 grid h-9 w-9 shrink-0 cursor-pointer place-items-center rounded-full text-red-700 hover:bg-red-50" aria-label="{{ __('admin.delete') }}">
            <x-admin.icon name="trash" class="h-4 w-4" />
        </button>
    </div>
    <div class="mt-3 flex items-center gap-3">
        @if (is_string($row['image'] ?? null) && $row['image'] !== '')
            <img src="{{ Storage::disk('public')->url($row['image']) }}" alt="" class="h-12 w-12 rounded-xl bg-white object-contain">
        @endif
        <label class="min-w-0 flex-1 text-sm">{{ __('admin.image') }}
            <input name="addons[{{ $index }}][image]" type="file" accept="image/*" class="mt-1 block w-full text-sm">
        </label>
    </div>
</div>
