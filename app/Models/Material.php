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

    protected $fillable = ['name', 'url'];

    protected $with = ['categories'];

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
        return $query->with('categories:id,name');
    }

    /**
     * Get the indexable data array for the model.
     *
     * @return array<string, mixed>
     */
    public function toSearchableArray(): array
    {
        return [
            'id' => (string) $this->id,
            'name' => $this->name,
            'categories' => $this->categories->pluck('name'),
            'created_at' => $this->created_at->timestamp,
        ];
    }

    public static function getTypesenseModelSettings(): array
    {
        return [
            'collection-schema' => [
                'fields' => [
                    [
                        'name' => 'id',
                        'type' => 'string',
                    ],
                    [
                        'name' => 'name',
                        'type' => 'string',
                    ],
                    [
                        'name' => 'categories',
                        'type' => 'string[]',
                    ],
                    [
                        'name' => 'created_at',
                        'type' => 'int64',
                    ],
                ],
                'default_sorting_field' => 'created_at',
            ],
            'search-parameters' => [
                'query_by' => 'name, categories',
            ],
        ];
    }
}
