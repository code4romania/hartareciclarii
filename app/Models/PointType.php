<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PointType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'service_type_id',
        'is_sgr',
    ];

    public function serviceType(): BelongsTo
    {
        return $this->belongsTo(ServiceType::class);
    }

    public function points(): HasMany
    {
        return $this->hasMany(Point::class);
    }
}
