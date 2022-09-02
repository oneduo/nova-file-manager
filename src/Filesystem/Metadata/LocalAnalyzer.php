<?php

declare(strict_types=1);

namespace BBSLab\NovaFileManager\Filesystem\Metadata;

use BBSLab\NovaFileManager\Filesystem\Support\GetID3;
use Illuminate\Support\Facades\Storage;

class LocalAnalyzer extends Analyzer
{
    protected function rawAnalyze(string $path): array
    {
        return (new GetID3())->analyze(Storage::disk($this->disk)->path($path));
    }
}
