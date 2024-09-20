<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Problem\Problem;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class Contribution extends Pivot
{
    protected $table = 'contributions';

    public const UPDATED_AT = null;

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
