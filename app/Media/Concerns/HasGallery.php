<?php

namespace App\Media\Concerns;

use App\Media\Models\GalleryImage;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasGallery
{
    /**
     * @return MorphMany<GalleryImage, $this>
     */
    public function images(): MorphMany
    {
        return $this->morphMany(GalleryImage::class, 'imageable')->orderBy('sort_order');
    }
}
