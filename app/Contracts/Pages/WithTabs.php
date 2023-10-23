<?php

/*
 * @Author: Bogdan Bocioaca
 * @Date:   2023-10-21 13:04:25
 * @Last Modified by:   Bogdan Bocioaca
 * @Last Modified time: 2023-10-21 13:04:42
 */

declare(strict_types=1);

namespace App\Contracts\Pages;

interface WithTabs
{
    public function getTabs(): array;
}
