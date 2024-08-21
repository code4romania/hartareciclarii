<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\IssueStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Issue extends Model
{
    use HasFactory;

    protected $casts = [
        'issues' => 'array',
        'status' => IssueStatus::class,
        'issueTypes.pivot.value' => 'array',
    ];

    protected $fillable = [
        'point_id',
        'user_id',
        'status',
        'type',
        'type_value',
        'description',
        'issues',
        'status_updated_at',
    ];

    protected $with = [
        'issueTypes',
    ];

    public function point(): BelongsTo
    {
        return $this->belongsTo(Point::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function serviceType(): BelongsTo
    {
        return $this->belongsTo(ServiceType::class);
    }

    public function issueTypes(): BelongsToMany
    {
        return $this->belongsToMany(IssueType::class)->using(IssueTypePivot::class)->withPivot('value');
    }

    public function changeStatus(string $status): void
    {
        $this->update([
            'status' => IssueStatus::from($status),
            'status_updated_at' => now(),
        ]);
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', IssueStatus::Pending);
    }
}
