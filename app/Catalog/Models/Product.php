<?php

namespace App\Catalog\Models;

use App\Media\Concerns\HasGallery;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'slug',
    'name',
    'description_ro',
    'description_ru',
    'details_ro',
    'details_ru',
    'image',
    'is_active',
    'sort_order',
])]
class Product extends Model
{
    use HasGallery;

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    /**
     * @return HasMany<ProductPrice, $this>
     */
    public function prices(): HasMany
    {
        return $this->hasMany(ProductPrice::class)->orderBy('sort_order');
    }

    /**
     * @return HasMany<ProductAddon, $this>
     */
    public function addons(): HasMany
    {
        return $this->hasMany(ProductAddon::class);
    }

    #[Scope]
    protected function active(Builder $query): void
    {
        $query->where('is_active', true);
    }
}
