<?php

declare(strict_types=1);

namespace BBSLab\NovaFileManager\Filesystem\Metadata;

use BBSLab\NovaFileManager\Contracts\Filesystem\Metadata\Analyzer as AnalyzerContract;

abstract class Analyzer implements AnalyzerContract
{
    public function __construct(
        public string $disk,
    ) {
    }

    public function analyze(string $path): array|object
    {
        return $this->rawAnalyze($path);
    }

    abstract protected function rawAnalyze(string $path): array;
}
