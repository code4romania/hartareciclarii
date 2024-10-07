<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Scout\Searchable;

class County extends Model
{
    use Searchable;
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
