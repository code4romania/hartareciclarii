<?php

declare(strict_types=1);

namespace App\Enums;

use App\Concerns\Enums\Arrayable;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum ReportStatus: string implements HasLabel, HasColor
{
    use Arrayable;

    case PENDING = 'pending';
    case IN_PROGRESS = 'in_progress';
    case COMPLETED = 'completed';
    case FAILED = 'failed';

    public function getLabel(): ?string
    {
        return __('enums.report_status.' . $this->value);
    }


    /** @deprecated use getLabel() */
    public function label(): ?string
    {
        return $this->getLabel();
    }

    public function getColor(): string|array|null
    {
        return match ($this->value) {
            'pending' => 'warning',
            'in_progress' => 'info',
            'completed' => 'success',
            'failed' => 'danger',
        };
    }
}
