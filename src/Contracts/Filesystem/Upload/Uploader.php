<?php

declare(strict_types=1);

namespace BBSLab\NovaFileManager\Contracts\Filesystem\Upload;

use BBSLab\NovaFileManager\Http\Requests\UploadFileRequest;

interface Uploader
{
    public function handle(UploadFileRequest $request, string $index = 'file'): array;
}
