<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ServiceType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    protected $casts = [
        'name' => 'string',
    ];

    public function issueTypes(): HasMany
    {
        return $this->hasMany(IssueType::class, 'service_type_id');
    }

    public function pointTypes(): HasMany
    {
        return $this->hasMany(PointType::class, 'service_type_id');
    }

    public function points(): HasMany
    {
        return $this->hasMany(Point::class, 'service_type_id');
    }

    public function issues(): HasMany
    {
        return $this->hasMany(Issue::class, 'service_type_id');
    }


}
