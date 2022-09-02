<?php

declare(strict_types=1);

namespace BBSLab\NovaFileManager\Filesystem\Metadata;

use BBSLab\NovaFileManager\Contracts\Filesystem\Metadata\Analyzer;
use Illuminate\Contracts\Filesystem\Filesystem;

class Factory
{
    public static function build(Filesystem $fileSystem): ?Analyzer
    {
        $driver = data_get($fileSystem->getConfig(), 'driver');

        return match ($driver) {
            'local' => new LocalAnalyzer($fileSystem),
            's3' => new S3Analyzer($fileSystem),
            'ftp' => new FTPAnalyzer($fileSystem),
            default => null,
        };
    }
}
