<?php

declare(strict_types=1);

namespace App\Enums\Point;

use App\Concerns\Enums\Arrayable;

enum ServiceType: string
{
    use Arrayable;

    case WASTE_COLLECTION = 'waste_collection';
    case REPAIRS = 'repairs';
    case REUSE = 'reuse';
    case REDUCE = 'reduce';
    case DONATIONS = 'donations';
    case OTHER = 'other';
}
