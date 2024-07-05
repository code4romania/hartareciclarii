<?php

declare(strict_types=1);

namespace App\Enums;

use App\Concerns\Enums\Arrayable;
use App\Concerns\Enums\HasLabel;

enum IssueStatus: string
{
    use Arrayable;
    use HasLabel;
    case New = 'new';
    case Solved = 'solved';
    case Pending = 'pending';
    case Denied = 'denied';

    public function labelKeyPrefix(): string
    {
        return 'enums.issue_status';
    }
}
