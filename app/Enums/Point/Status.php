<?php

declare(strict_types=1);

namespace App\Enums\Point;

use App\Concerns\Enums\Arrayable;
use App\Concerns\Enums\Comparable;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum Status: string implements HasColor, HasIcon, HasLabel
{
    use Arrayable;
    use Comparable;

    case VERIFIED = 'verified';
    case UNVERIFIED = 'unverified';
    case WITH_PROBLEMS = 'with_problems';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::VERIFIED => __('enums.point_status.verified'),
            self::UNVERIFIED => __('enums.point_status.unverified'),
            self::WITH_PROBLEMS => __('enums.point_status.with_problems'),
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::VERIFIED => 'success',
            self::UNVERIFIED => 'warning',
            self::WITH_PROBLEMS => 'danger',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::VERIFIED => 'heroicon-m-check-circle',
            self::UNVERIFIED => 'heroicon-m-question-mark-circle',
            self::WITH_PROBLEMS => 'heroicon-m-exclamation-triangle',
        };
    }

    /**
     * @deprecated use getLabel() instead
     */
    public function label(): string
    {
        return $this->getLabel();
    }

    /**
     * @deprecated use getColor() instead
     */
    public function color(): string
    {
        return $this->getColor();
    }
}
