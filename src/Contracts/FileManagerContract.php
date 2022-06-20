<?php

declare(strict_types=1);

namespace BBSLab\NovaFileManager\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface FileManagerContract
{
    public function paginate(Collection $data): LengthAwarePaginator;
}
