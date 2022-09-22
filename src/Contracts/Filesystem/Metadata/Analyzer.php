<?php

declare(strict_types=1);

namespace Oneduo\NovaFileManager\Contracts\Filesystem\Metadata;

interface Analyzer
{
    public function analyze(string $path): array|object;
}
