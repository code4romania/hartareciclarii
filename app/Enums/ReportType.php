<?php

declare(strict_types=1);

namespace App\Enums;

use App\Concerns\Enums\Arrayable;
use Filament\Support\Contracts\HasLabel;

enum ReportType: string implements HasLabel
{
    use Arrayable;

    case POINTS = 'points';
    case PROBLEMS = 'problems';
    case USER_ACTIVITY = 'user_activity';
    case TOP_USERS = 'top_users';

    public function getLabel(): ?string
    {
        return __('enums.report_type.' . $this->value);
    }

    /** @deprecated use getLabel() */
    public function label(): ?string
    {
        return $this->getLabel();
    }
}
