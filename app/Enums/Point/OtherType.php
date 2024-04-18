<?php

declare(strict_types=1);

namespace App\Enums\Point;

use App\Concerns\Enums\Arrayable;
use App\Concerns\Enums\HasLabel;

enum OtherType: string
{
    use Arrayable;
    use HasLabel;

    case OTHER = 'other';

    public function labelKeyPrefix(): string
    {
        return 'enums.point.other_types';
    }
}
