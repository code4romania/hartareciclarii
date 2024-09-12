<?php

declare(strict_types=1);

namespace App\Models\Problem;

use App\Models\ServiceType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProblemType extends Model
{
    protected $fillable = [
        'code',
        'name',
    ];

    public function serviceTypes(): BelongsToMany
    {
        return $this->belongsToMany(ServiceType::class);
    }

    public function problems(): HasMany
    {
        return $this->hasMany(Problem::class);
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }
}
