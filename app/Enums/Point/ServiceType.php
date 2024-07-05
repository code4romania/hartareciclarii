<?php

declare(strict_types=1);

namespace App\Enums\Point;

use App\Concerns\Enums\Arrayable;
use App\Concerns\Enums\HasLabel;
use App\Enums\Issue\DefaultIssueType;
use App\Enums\Issue\RepairsIssueType;
use App\Enums\Issue\WasteCollectionIssueType;

enum ServiceType: string
{
    use Arrayable;
    use HasLabel;

    case WASTE_COLLECTION = 'waste_collection';
    case REPAIRS = 'repairs';
    case REUSE = 'reuse';
    case REDUCTION = 'reduction';
    case DONATIONS = 'donations';
    case OTHER = 'other';

    public function labelKeyPrefix(): string
    {
        return 'enums.point.service_types';
    }

    public function pointTypes(): string
    {
        return match ($this) {
            self::WASTE_COLLECTION => WestCollectionType::class,
            self::REPAIRS => RepairsType::class,
            self::REUSE => ReuseType::class,
            self::REDUCTION => ReductionType::class,
            self::DONATIONS => DonationsType::class,
            self::OTHER => OtherType::class,
        };
    }

    public function issueTypes():string
    {
        return match ($this) {
            self::WASTE_COLLECTION => WasteCollectionIssueType::class,
            self::REPAIRS => RepairsIssueType::class,
            self::REUSE, self::REDUCTION, self::DONATIONS, self::OTHER => DefaultIssueType::class,
        };

    }

    public function issueFields(string $type): array
    {
        return match ($this) {
            self::WASTE_COLLECTION => WasteCollectionIssueType::fields($type),
            self::REPAIRS => RepairsIssueType::fields($type),
            self::REUSE, self::REDUCTION, self::DONATIONS, self::OTHER => DefaultIssueType::fields($type),
        };
    }
}
