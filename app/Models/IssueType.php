<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class IssueType extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'type',
    ];
    protected $casts = [
      'issues.pivot.value' => 'json',
    ];

    public function serviceType(): BelongsTo
    {
        return $this->belongsTo(ServiceType::class);
    }

    public function issues(): BelongsToMany
    {
        return $this->belongsToMany(Issue::class)->using(IssueTypePivot::class)->withPivot('value');
    }
}
