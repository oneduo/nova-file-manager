<?php

declare(strict_types=1);

namespace Oneduo\NovaFileManager\Contracts\Filesystem\Upload;

use Oneduo\NovaFileManager\Http\Requests\UploadFileRequest;

interface Uploader
{
    public function handle(UploadFileRequest $request, string $index = 'file'): array;
}
