<?php

declare(strict_types=1);

namespace App\Enums;

use App\Concerns\Enums\Arrayable;
use Filament\Support\Contracts\HasLabel;

enum ProblemStatus: string implements HasLabel
{
    use Arrayable;

    case NEW = 'new';
    case PENDING = 'pending';
    case CLOSED = 'closed';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::NEW => __('enums.problem_status.new'),
            self::PENDING => __('enums.problem_status.pending'),
            self::CLOSED => __('enums.problem_status.closed'),
        };
    }

    /** @deprecated use getLabel() */
    public function label(): ?string
    {
        return $this->getLabel();
    }
}
