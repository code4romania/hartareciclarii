<?php

declare(strict_types=1);

namespace App\Enums\Point;

use App\Concerns\Enums\Arrayable;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum Status: string implements HasColor, HasIcon, HasLabel
{
    use Arrayable;

    case VERIFIED = 'verified';
    case NEEDS_VERIFICATION = 'needs_verification';
    case WITH_PROBLEMS = 'with_problems';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::VERIFIED => __('enums.point_status.verified'),
            self::NEEDS_VERIFICATION => __('enums.point_status.needs_verification'),
            self::WITH_PROBLEMS => __('enums.point_status.with_problems'),
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::VERIFIED => 'info',
            self::NEEDS_VERIFICATION => 'warning',
            self::WITH_PROBLEMS => 'danger',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::VERIFIED => 'check-circle',
            self::NEEDS_VERIFICATION => 'exclamation-circle',
            self::WITH_PROBLEMS => 'exclamation-triangle',
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
