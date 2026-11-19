<div class="flex items-center gap-2" data-value-row>
    <input name="{{ $name }}[{{ $index }}]" type="{{ $type }}" value="{{ $value }}" class="admin-input w-full">
    <button type="button" data-remove-row class="grid h-9 w-9 shrink-0 cursor-pointer place-items-center rounded-full text-red-700 hover:bg-red-50" aria-label="{{ __('admin.delete') }}">
        <x-admin.icon name="trash" class="h-4 w-4" />
    </button>
</div>
