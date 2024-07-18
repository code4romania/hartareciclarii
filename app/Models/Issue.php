<?php

namespace App\Models;

use App\Enums\IssueStatus;
use App\Enums\Point\ServiceType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Issue extends Model
{
    use HasFactory;

    protected $casts = [
        'issues' => 'array',
        'type' => ServiceType::class,
        'status' => IssueStatus::class
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

}
