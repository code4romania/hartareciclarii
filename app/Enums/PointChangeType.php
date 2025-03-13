<?php

declare(strict_types=1);

namespace App\Enums;

use App\Concerns\Enums\Arrayable;
use App\Concerns\Enums\HasLabel;

enum PointChangeType: string
{
    use Arrayable;
    use HasLabel;



    public function labelKeyPrefix(): string
    {
        return 'enums.issue_status';
    }
}
