<?php

declare(strict_types=1);

namespace App\Enums\Issue;

use App\Concerns\Enums\Arrayable;
use App\Concerns\Enums\HasLabel;

enum RepairsIssueType: string
{
    use Arrayable;
    use HasLabel;
    case INCORRECT_ADDRESS = 'repairs_issues_incorrect_address';
    case INCORRECT_MAP_POINT = 'repairs_issues_incorrect_map_point';
    case INCORRECT_REPAIRED_PRODUCTS = 'repairs_issues_incorrect_repaired_products';
    case INCORRECT_SCHEDULE = 'repairs_issues_incorrect_schedule';
    case REFUSED_REPAIR = 'repairs_issues_refused_repair';
    case OTHER = 'repairs_issues_other';

    public function labelKeyPrefix(): string
    {
        return 'enums.issue.repairs_issue_types';
    }

    public static function fields($field): array
    {
       return match ($field){
            self::INCORRECT_ADDRESS->value => [
                ['label' => 'enums.issue.fields.address', 'type' => 'text'],
            ],
            self::INCORRECT_MAP_POINT->value => [
                ['label' => 'enums.issue.fields.latitude', 'type' => 'number'],
                ['label' => 'enums.issue.fields.longitude', 'type' => 'number'],
            ],
            self::INCORRECT_REPAIRED_PRODUCTS->value => [
                ['label' => 'enums.issue.fields.repaired_products_refused', 'type' => 'checkbox'],
                ['label' => 'enums.issue.fields.repaired_products_not_repaired', 'type' => 'checkbox'],
                ['label' => 'enums.issue.fields.repaired_products_other', 'type' => 'text'],
            ],
            self::INCORRECT_SCHEDULE->value => [
                ['label' => 'enums.issue.fields.schedule', 'type' => 'text'],
            ],
            self::REFUSED_REPAIR->value => [
                ['label' => 'enums.issue.fields.repaired_products_refused', 'type' => 'checkbox'],
                ['label' => 'enums.issue.fields.repaired_products_not_repaired', 'type' => 'checkbox'],
            ],
            self::OTHER->value => [],
       };
    }
}
