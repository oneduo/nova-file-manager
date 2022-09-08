<?php

declare(strict_types=1);

namespace BBSLab\NovaFileManager\Contracts\Support;

use Closure;

interface ResolvesUrl
{
    public function hasUrlResolver(): bool;

    public function getUrlResolver(): ?Closure;

    public function resolveUrlUsing(Closure $resolver): static;
}
