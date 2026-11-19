@props(['images' => []])

<div data-attachments>
    <div class="flex flex-wrap gap-3">
        @foreach ($images as $image)
            <figure data-attachment class="group relative h-24 w-24 overflow-hidden rounded-xl bg-[#f7f5f2]">
                <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($image->path) }}" alt="" class="h-full w-full object-cover">
                <input type="hidden" name="keep_images[]" value="{{ $image->id }}">
                <button type="button" data-remove-attachment class="absolute top-1 right-1 grid h-7 w-7 cursor-pointer place-items-center rounded-full bg-white text-red-700 shadow-sm" aria-label="{{ __('admin.delete') }}">
                    <x-admin.icon name="trash" class="h-4 w-4" />
                </button>
            </figure>
        @endforeach
        <div data-attachment-pending class="contents"></div>
    </div>
    <label data-dropzone class="admin-fill mt-3 flex cursor-pointer flex-col items-center justify-center rounded-2xl px-4 py-8 text-center text-sm text-stone-600 transition">
        <x-admin.icon name="image" class="mb-2 h-6 w-6 text-stone-500" />
        <span>{{ __('admin.drop_images') }}</span>
        <input type="file" name="images[]" accept="image/*" multiple class="sr-only" data-attachment-input>
    </label>
</div>
