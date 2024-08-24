<?php

declare(strict_types=1);

namespace App\Models;

use Spatie\MediaLibrary\HasMedia;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

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
