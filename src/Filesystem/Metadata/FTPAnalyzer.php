<?php

declare(strict_types=1);

namespace BBSLab\NovaFileManager\Filesystem\Metadata;

use Illuminate\Support\Facades\Storage;

class FTPAnalyzer extends Analyzer
{
    protected function rawAnalyze(string $path): array
    {
        /** @var \Illuminate\Filesystem\FilesystemAdapter $filesystem */
        $filesystem = Storage::disk($this->disk);

        $config = $filesystem->getConfig();
        $host = data_get($config, 'host');
        $username = data_get($config, 'username');
        $password = data_get($config, 'password');
        $port = data_get($config, 'port', 21);

        return (new GetID3())->analyze(
            filename: sprintf('ftp://%s:%s@%s:%s/%s', $username, $password, $host, $port, $path),
            fp: $filesystem->readStream($path),
        );
    }
}