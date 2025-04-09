<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ReportStatus;
use App\Enums\ReportType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Report extends Model
{
    protected $guarded = ['id'];

    protected $fillable = [
        'results',
        'type',
        'filters',
        'status',
        'label',
        'created_by_id',
    ];

    protected $casts = [
        'results' => 'array',
        'filters' => 'array',
        'types' => ReportType::class,
        'status' => ReportStatus::class,

    ];

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }
}
