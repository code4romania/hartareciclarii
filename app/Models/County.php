<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use MatanYadaev\EloquentSpatial\Objects\Polygon;
use MatanYadaev\EloquentSpatial\Traits\HasSpatial;

class County extends Model
{
    use HasSpatial;

    public $timestamps = false;

    protected $fillable = [
        'pol',
        'name',
        'siruta',
    ];

    protected $casts = [
        'pol' => Polygon::class,
    ];

    public function cities(): HasMany
    {
        return $this->hasMany(City::class);
    }
}
