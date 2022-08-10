<?php

declare(strict_types=1);

namespace BBSLab\NovaFileManager\Filesystem\Metadata;

use BBSLab\NovaFileManager\Contracts\Filesystem\Metadata\Analyzer as AnalyzerContract;
use Illuminate\Support\Facades\Cache;

abstract class Analyzer implements AnalyzerContract
{
    public function __construct(
        public string $disk,
    ) {
    }

    public function analyze(string $path): array|object
    {
        $shouldCache = config('nova-file-manager.file_analysis.cache.enable');

        if (!$shouldCache) {
            return $this->rawAnalyze($path);
        }

        return Cache::remember(
            key: "nova-file-manager:analysis:{$path}",
            ttl: config('nova-file-manager.file_analysis.cache.ttl_in_seconds'),
            callback: fn() => $this->rawAnalyze($path)
        );
    }

    abstract protected function rawAnalyze(string $path): array;
}
