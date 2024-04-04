<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class County extends Model
{
    protected $fillable = [
        'pol',
        'name',
        'siruta',
    ];
    public $timestamps = false;

    public function cities(): HasMany
    {
        return $this->hasMany(City::class);
    }
}
