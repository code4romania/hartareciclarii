<?php

declare(strict_types=1);

namespace App\Enums\Issue;

use App\Concerns\Enums\Arrayable;
use App\Concerns\Enums\HasLabel;

enum DefaultIssueType: string
{
    use Arrayable;
    use HasLabel;
    // Adresa nu este corectă (PRB_Tip_01)
    // Locația punctului pe hartă nu este corectă (PRB_Tip_02)
    // Altă problemă (PRB_Tip_99)

    case INCORRECT_ADDRESS = 'default_incorrect_address';

    case INCORRECT_MAP_POINT = 'default_incorrect_map_point';

    case OTHER = 'default_other';

    public function labelKeyPrefix(): string
    {
        return 'enums.issue.default_issue_types';
    }

    public static function fields($field): array
    {
        return match ($field) {
            self::INCORRECT_ADDRESS->value => [
                ['label' => 'enums.issue.fields.address', 'type' => 'text'],
            ],
            self::INCORRECT_MAP_POINT->value => [
                ['label' => 'enums.issue.fields.latitude', 'type' => 'number'],
                ['label' => 'enums.issue.fields.longitude', 'type' => 'number'],
            ],
            self::OTHER->value => [],
        };
    }
}
