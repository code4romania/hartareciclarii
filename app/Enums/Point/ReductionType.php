<?php

declare(strict_types=1);

namespace App\Enums\Point;

use App\Concerns\Enums\Arrayable;
use App\Concerns\Enums\HasLabel;

enum ReductionType: string
{
    use Arrayable;
    use HasLabel;

    case ZERO_WASTE_SHOP = 'zero_waste_shop';
    case FREE_WATER_LOCATION = 'free_water_location';
    case BULK_SALES_SHOPS = 'bulk_sales_shops';
    case SHOPS_ACCEPTING_OWN_PACKAGING = 'shops_accepting_own_packaging';
    case OTHER_REDUCTION_SERVICES = 'other_reduction_services';
    public function labelKeyPrefix(): string
    {
        return 'enums.point.reduction_types';
    }
}
