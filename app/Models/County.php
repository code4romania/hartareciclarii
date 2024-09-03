<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class County extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'name',
        'siruta',
    ];

    public function cities(): HasMany
    {
        return $this->hasMany(City::class);
    }
}
