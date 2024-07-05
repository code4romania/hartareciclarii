<?php

declare(strict_types=1);

namespace App\Enums\Issue;

use App\Concerns\Enums\Arrayable;
use App\Concerns\Enums\HasLabel;

enum WasteCollectionIssueType: string
{
    use Arrayable;
    use HasLabel;
    case INCORRECT_ADDRESS = 'waste_collection_incorrect_address';
    case INCORRECT_MAP_POINT = 'waste_collection_incorrect_map_point';
    case INCORRECT_MATERIALS = 'waste_collection_incorrect_materials';
    case INCORRECT_SCHEDULE = 'waste_collection_incorrect_schedule';
    case MALFUNCTIONING_CONTAINER = 'waste_collection_malfunctioning_container';
    case REFUSED_COLLECTION = 'waste_collection_refused_collection';
    case OTHER = 'other';

    public function labelKeyPrefix(): string
    {
        return 'enums.issues.waste_collection_issue_types';
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
            self::INCORRECT_MATERIALS->value => [

                ['label' => 'enums.issue.fields.incorrect_materials_missing', 'type' => 'checkbox'],
                ['label' => 'enums.issue.fields.incorrect_materials_extra', 'type' => 'checkbox'],
                ['label' => 'enums.issue.fields.incorrect_materials_other', 'type' => 'checkbox'],
            ],
            self::INCORRECT_SCHEDULE->value => [
                ['label' => 'enums.issue.fields.schedule', 'type' => 'text'],
            ],
            self::MALFUNCTIONING_CONTAINER->value => [
                ['label' => 'enums.issue.fields.container', 'type' => 'text'],
            ],
            self::REFUSED_COLLECTION->value => [
                ['label' => 'enums.issue.fields.refused_collection_type_of_material', 'type' => 'checkbox'],
                ['label' => 'enums.issue.fields.refused_collection_quantity', 'type' => 'checkbox'],
                ['label' => 'enums.issue.fields.refused_collection_other', 'type' => 'checkbox'],
            ],
            self::OTHER->value => [],
        };
    }
}
