<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class MaterialCategory extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;

    protected $fillable = [
        'name',
        'position',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope('order', fn (Builder $builder) => $builder->orderBy('position'));
    }

    public function materials(): MorphToMany
    {
        return $this->morphToMany(Material::class, 'model', 'model_has_materials');
    }
}
