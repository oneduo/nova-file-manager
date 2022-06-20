<?php

declare(strict_types=1);

namespace BBSLab\NovaFileManager\Contracts\Filesystem\Upload;

use BBSLab\NovaFileManager\Http\Requests\UploadRequest;

interface Uploader
{
    public function handle(UploadRequest $request, string $index = 'file'): array;
}
