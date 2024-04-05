<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\Point\ServiceType;
use App\Enums\Point\Status;
use Database\Factories\PointFactory;
use Database\Factories\TestFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Point extends Model
{
    use HasFactory;

    protected $fillable = [
        'latitude',
        'longitude',
        'address',
        'name',
        'phone',
        'email',
        'website',
        'notes',
        'observations',
        'schedule',
        'status',
        'service_type',
        'point_type',

    ];

    protected $casts = [
        'schedule' => 'array',
        'status' => Status::class,
        'service_type' => ServiceType::class,
    ];

    public function materials(): BelongsToMany
    {
        return $this->belongsToMany(Material::class);
    }

    public function getPointTypeEnumAttribute()
    {
        return $this->service_type->pointTypes();
    }
}
