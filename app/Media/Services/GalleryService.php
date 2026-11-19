<?php

namespace App\Media\Services;

use App\Media\Models\GalleryImage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class GalleryService
{
    /**
     * @param  list<int|string>  $keepIds
     * @param  array<int, UploadedFile|null>  $uploads
     */
    public function sync(Model $owner, array $keepIds, array $uploads): void
    {
        $keep = array_map(intval(...), $keepIds);

        $owner->images()->whereNotIn('id', $keep)->get()->each(function (GalleryImage $image): void {
            Storage::disk('public')->delete($image->path);
            $image->delete();
        });

        $sort = (int) $owner->images()->max('sort_order');

        foreach ($uploads as $upload) {
            if (! $upload instanceof UploadedFile) {
                continue;
            }

            $sort++;
            $owner->images()->create([
                'path' => $upload->store('gallery', 'public'),
                'sort_order' => $sort,
            ]);
        }

        $owner->images()->orderBy('sort_order')->get()->values()->each(function (GalleryImage $image, int $index): void {
            $image->update(['sort_order' => $index]);
        });

        if (in_array('image', $owner->getFillable(), true)) {
            $owner->forceFill([
                'image' => $owner->images()->orderBy('sort_order')->value('path'),
            ])->save();
        }
    }
}
