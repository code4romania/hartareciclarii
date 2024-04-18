<?php

declare(strict_types=1);

namespace App\Enums\Point;

use App\Concerns\Enums\Arrayable;
use App\Concerns\Enums\HasLabel;

enum RepairsType: string
{
    use Arrayable;
    use HasLabel;

    case TAILORING = 'tailoring';
    case SHOE_REPAIRS = 'shoe_repairs';
    case WATCHMAKING = 'watchmaking';
    case JEWELRY_REPAIRS = 'jewelry_repairs';
    case ELECTRONIC_REPAIRS = 'electronic_repairs';
    case HOUSEHOLD_APPLIANCES_REPAIRS = 'household_appliances_repairs';
    case IT_REPAIRS = 'it_repairs';
    case PHONE_REPAIRS = 'phone_repairs';
    case FURNITURE_REPAIRS = 'furniture_repairs';
    case BICYCLE_REPAIRS = 'bicycle_repairs';
    case OTHER_REPAIRS = 'other_repairs';

    public function labelKeyPrefix(): string
    {
        return 'enums.point.repairs_types';
    }
}
