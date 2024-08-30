<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Laravel\Scout\Searchable;

class Material extends Model
{
    use HasFactory;
    use Searchable;

    protected $fillable = ['name'];

    public function categories(): MorphToMany
    {
        return $this->morphedByMany(MaterialCategory::class, 'model', 'model_has_materials');
    }

    public function points(): BelongsToMany
    {
        return $this->belongsToMany(Point::class);
    }

    public function icon(): Attribute
    {
        return new Attribute(fn () => $this->categories->first()->getFirstMediaUrl());
    }

    protected function makeAllSearchableUsing(Builder $query): Builder
    {
        return $query->with('categories');
    }

    /**
     * Get the indexable data array for the model.
     *
     * @return array<string, mixed>
     */
    public function toSearchableArray(): array
    {
        return [
            'name' => $this->name,
            // 'categories' => $this->categories->pluck('name'),
        ];
    }
}
