<?php

declare(strict_types=1);

namespace App\Enums\Point;

use App\Concerns\Enums\Arrayable;
use App\Concerns\Enums\HasLabel;

enum Status: string
{
    use Arrayable;
    use HasLabel;
    case VERIFIED = 'verified';
    case NEEDS_VERIFICATION = 'needs_verification';
    case WITH_PROBLEMS = 'with_problems';

    public function labelKeyPrefix(): string
    {
        return 'enums.point_status';
    }

    public function color(): string
    {
        return match ($this) {
            self::VERIFIED => 'info',
            self::NEEDS_VERIFICATION => 'warning',
            self::WITH_PROBLEMS => 'danger',
        };
    }
}
