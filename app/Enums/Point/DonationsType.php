<?php

declare(strict_types=1);

namespace App\Enums\Point;

use App\Concerns\Enums\Arrayable;
use App\Concerns\Enums\HasLabel;

enum DonationsType: string
{
    use Arrayable;
    use HasLabel;


    case DONATION_CENTER = 'donation_center';
    public function labelKeyPrefix(): string
    {
        return 'enums.point.donation_types';
    }
}
