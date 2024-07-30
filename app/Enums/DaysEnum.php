<?php

namespace App\Enums;

use App\Concerns\Enums\Arrayable;
use App\Concerns\Enums\HasLabel;

enum DaysEnum:string
{
    use HasLabel;
    use Arrayable;
    case Monday = 'monday';
    case Tuesday = 'tuesday';
    case Wednesday = 'wednesday';
    case Thursday = 'thursday';
    case Friday = 'friday';
    case Saturday = 'saturday';
    case Sunday = 'sunday';

    public function labelKeyPrefix(): string
    {
        return 'enums.days';
    }
}
