<?php

declare(strict_types=1);

namespace BBSLab\NovaFileManager\Filesystem\Metadata;

use BBSLab\NovaFileManager\Contracts\Filesystem\Metadata\Analyzer;

class Factory
{
    public static function build(string $disk): ?Analyzer
    {
        $driver = config('filesystems.disks.'.$disk.'.driver');

        return match ($driver) {
            'local' => new LocalAnalyzer($disk),
            's3' => new S3Analyzer($disk),
            'ftp' => new FTPAnalyzer($disk),
            default => null,
        };
    }
}
