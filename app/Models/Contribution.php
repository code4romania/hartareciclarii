<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Problem\Problem;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class Contribution extends Pivot
{
    protected $table = 'contributions';

    public const UPDATED_AT = null;

    public $incrementing = false;

    protected static function booted(): void
    {
        static::addGlobalScope('latest', function (Builder $query) {
            $query->orderByDesc('created_at');
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function model(): MorphTo
    {
        return $this->morphTo();
    }

    public function getKey(): string
    {
        return "{$this->user_id}-{$this->model_id}-{$this->model_type}";
    }

    public function getPoint(): ?Point
    {
        return match ($this->model_type) {
            (new Point)->getMorphClass() => $this->model,
            (new Problem)->getMorphClass() => $this->model?->point,
            default => null,
        };
    }

    protected function pointId(): Attribute
    {
        return Attribute::get(fn (): ?int => $this->getPoint()?->id);
    }

    protected function pointTypeName(): Attribute
    {
        return Attribute::get(fn (): ?string => $this->getPoint()?->pointType?->name);
    }

    protected function problemTypeName(): Attribute
    {
        return Attribute::get(function (): ?string {
            if ($this->model_type !== (new Problem)->getMorphClass()) {
                return null;
            }

            return $this->model?->type?->name;
        });
    }

    protected function contributionAddress(): Attribute
    {
        return Attribute::get(fn (): ?string => $this->getPoint()?->address);
    }

    public function scopeWithPointData(Builder $query): Builder
    {
        return $query->with('model', function (MorphTo $morphTo) {
            $morphTo->morphWith([
                Point::class => ['pointType'],
                Problem::class => ['point.pointType', 'type'],
            ]);
        });
    }
}
