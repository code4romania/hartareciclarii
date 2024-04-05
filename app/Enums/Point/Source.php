<?php

declare(strict_types=1);

namespace App\Enums\Point;

use App\Concerns\Enums\Arrayable;
use App\Concerns\Enums\HasLabel;

enum Source: string
{
    use Arrayable;
    use HasLabel;

    case IMPORT = 'import';
    case MANUAL_BY_ADMIN = 'manual_by_admin';
    case MANUAL_BY_USER = 'manual_by_user';

    public function labelKeyPrefix(): string
    {
        return 'enums.point.sources';
    }
}
