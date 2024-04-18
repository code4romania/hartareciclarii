<?php

declare(strict_types=1);

namespace App\Enums\Point;

use App\Concerns\Enums\Arrayable;
use App\Concerns\Enums\HasLabel;

enum WestCollectionType: string
{
    use Arrayable;
    use HasLabel;
    case STREET_CONTAINER = 'street_container';
    case SHOP = 'shop';
    case WASTE_RECOVERY = 'waste_recovery';
    case VOLUNTEER_CENTER = 'volunteer_center';

    public function labelKeyPrefix(): string
    {
        return 'enums.point.west_collection_types';
    }
}
