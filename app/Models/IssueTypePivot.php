<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class IssueTypePivot extends Pivot
{
    protected $table = 'issue_issue_type';

    protected $fillable = [
        'issue_id',
        'issue_type_id',
        'value',
    ];

    protected $casts = [
        'value' => 'array',
    ];

    public function issue(): BelongsTo
    {
        return $this->belongsTo(Issue::class);
    }

    public function issueType(): BelongsTo
    {
        return $this->belongsTo(IssueType::class);
    }
}
