<?php

declare(strict_types=1);

namespace App\Models\Problem;

use App\Enums\ProblemStatus;
use App\Models\Contribution;
use App\Models\Material;
use App\Models\Point;
use App\Models\User;
use Database\Factories\ProblemFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use MatanYadaev\EloquentSpatial\Objects\Point as SpatialPoint;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Problem extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;

    protected static string $factory = ProblemFactory::class;

    protected $fillable = [
        'point_id',
        'reported_by',
        'type_id',
        'county_id',
        'city_id',
        'address',
        'location',
        'description',
        'started_at',
        'closed_at',
    ];

    protected $casts = [
        'location' => SpatialPoint::class,
        'started_at' => 'datetime',
        'closed_at' => 'datetime',
    ];

    public function point(): BelongsTo
    {
        return $this->belongsTo(Point::class);
    }

    public function reporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reported_by');
    }

    public function type(): BelongsTo
    {
        return $this->belongsTo(ProblemType::class);
    }

    public function contribution(): MorphOne
    {
        return $this->morphOne(Contribution::class, 'model');
    }

    public function subTypes(): BelongsToMany
    {
        return $this->belongsToMany(ProblemType::class, 'problem_has_subtype', 'problem_id', 'subtype_id');
    }

    public function materials(): MorphToMany
    {
        return $this->morphToMany(Material::class, 'model', 'model_has_materials')
            ->withPivot(['flag'])
            ->withCasts(['flag' => 'boolean']);
    }

    public function scopeWhereNew(Builder $query): Builder
    {
        return $query->whereNull('started_at');
    }

    public function scopeWherePending(Builder $query): Builder
    {
        return $query->whereNotNull('started_at');
    }

    public function scopeWhereOpen(Builder $query): Builder
    {
        return $query->whereNull('closed_at');
    }

    public function scopeWhereClosed(Builder $query): Builder
    {
        return $query->whereNotNull('closed_at');
    }

    public function changeStatus(string $status): self
    {
        $status = ProblemStatus::tryFrom($status);

        $problem = match ($status) {
            ProblemStatus::NEW => $this->markAsNew(),
            ProblemStatus::PENDING => $this->markAsPending(),
            ProblemStatus::CLOSED => $this->markAsClosed(),
            default => throw new \Exception('Unexpected match value'),
        };
        $problem->save();

        return $problem;
    }

    public function markAsNew(): self
    {
        return $this->fill([
            'started_at' => null,
            'closed_at' => null,
        ]);
    }

    public function markAsPending(): self
    {
        return $this->fill([
            'started_at' => now(),
            'closed_at' => null,
        ]);
    }

    public function markAsClosed(): self
    {
        return $this->fill([
            'closed_at' => now(),
        ]);
    }

    protected function collectedMaterials(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->materials->where('pivot.flag', true)->pluck('name'),
        );
    }

    protected function notCollectedMaterials(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->materials->where('pivot.flag', false)->pluck('name'),
        );
    }

    public function status(): Attribute
    {
        return Attribute::make(function () {
            if ($this->closed_at) {
                return ProblemStatus::CLOSED;
            }

            if ($this->started_at) {
                return ProblemStatus::PENDING;
            }

            return ProblemStatus::NEW;
        });
    }
}
