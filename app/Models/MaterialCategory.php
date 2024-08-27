<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class MaterialCategory extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;

    protected $fillable = [
        'name',
        'category_rank',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope('order', fn (Builder $builder) => $builder->orderBy('category_rank'));
    }

    public function materials(): BelongsToMany
    {
        return $this->belongsToMany(Material::class);
    }
}
