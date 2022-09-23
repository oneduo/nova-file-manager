<?php

declare(strict_types=1);

namespace Oneduo\NovaFileManager\Traits\Support;

use Closure;

trait ResolvesUrl
{
    protected Closure $urlResolver;

    public function getUrlResolver(): ?Closure
    {
        return $this->urlResolver ?? null;
    }

    public function hasUrlResolver(): bool
    {
        return isset($this->urlResolver) && is_callable($this->urlResolver);
    }

    public function resolveUrlUsing(Closure $resolver): static
    {
        $this->urlResolver = $resolver;

        return $this;
    }
}
