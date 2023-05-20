<?php

declare(strict_types=1);

namespace Oneduo\NovaFileManager\Filesystem\Metadata;

use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Facades\Cache;
use Oneduo\NovaFileManager\Contracts\Filesystem\Metadata\Analyzer as AnalyzerContract;

abstract class Analyzer implements AnalyzerContract
{
    public function __construct(
        public Filesystem $disk,
    ) {
    }

    public function analyze(string $path): array|object
    {
        $shouldCache = config('nova-file-manager.file_analysis.cache.enabled');

        if (! $shouldCache) {
            return $this->rawAnalyze($path);
        }

        return Cache::remember(
            key: "nova-file-manager:analysis:{$path}",
            ttl: config('nova-file-manager.file_analysis.cache.ttl_in_seconds'),
            callback: fn () => $this->rawAnalyze($path)
        );
    }

    abstract protected function rawAnalyze(string $path): array;
}
