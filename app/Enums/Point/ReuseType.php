<?php

declare(strict_types=1);

namespace App\Enums\Point;

use App\Concerns\Enums\Arrayable;
use App\Concerns\Enums\HasLabel;

enum ReuseType: string
{
    use Arrayable;
    use HasLabel;

    case SECOND_HAND_CLOTHING_STORE = 'second_hand_clothing_store';
    case SECOND_HAND_ELECTRONICS_STORE = 'second_hand_electronics_store';
    case VINTAGE_AND_ANTIQUE_STORE = 'vintage_and_antique_store';
    case ANTIQUARIAT = 'antiquariat';
    case CONSIGNMENT = 'consignment';
    case RETURNABLE_PACKAGING_STORE = 'returnable_packaging_store';
    case GREEN_TRANSPORT_RENTAL = 'green_transport_rental';
    case SPORTS_EQUIPMENT_RENTAL = 'sports_equipment_rental';
    case EVENT_OBJECTS_RENTAL = 'event_objects_rental';
    case IT_EQUIPMENT_RENTAL = 'it_equipment_rental';
    case OTHER_RENTAL_SERVICES = 'other_rental_services';
    case UPCYCLING = 'upcycling';
    case BARTER = 'barter';
    case OTHER_REUSE_SERVICES = 'other_reuse_services';

    public function labelKeyPrefix(): string
    {
        return 'enums.point.reuse_types';
    }
}
