<?php

declare(strict_types=1);

namespace App\Concerns;

use Illuminate\Database\Eloquent\Model;

trait CanAssignContributor
{
    public function assignContributor(Model $model): void
    {
        if (auth()->guest()) {
            return;
        }

        $model->contribution()->create([
            'user_id' => auth()->id(),
        ]);
    }
}
