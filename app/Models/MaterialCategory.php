<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class MaterialCategory extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'category_rank'];

    public function materials(): BelongsToMany
    {
        return $this->belongsToMany(Material::class);
    }
}
