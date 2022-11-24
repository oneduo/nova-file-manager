<?php

declare(strict_types=1);

namespace Oneduo\NovaFileManager\Events;

use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Foundation\Events\Dispatchable;

class FileUploading
{
    use Dispatchable;

    public function __construct(public Filesystem $filesystem, public string $disk, public string $path)
    {
    }
}
