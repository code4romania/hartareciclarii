<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class City extends Model
{
    protected $table = 'cities';

    public function county(): BelongsTo
    {
        return $this->belongsTo(County::class);
    }

    protected function getNameWithCountyAttribute(): string
    {
        return "{$this->name}, {$this->county->name}";
    }
}
