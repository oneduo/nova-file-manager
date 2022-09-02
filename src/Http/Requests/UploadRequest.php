<?php

declare(strict_types=1);

namespace BBSLab\NovaFileManager\Http\Requests;

use BBSLab\NovaFileManager\Rules\DiskExistsRule;
use BBSLab\NovaFileManager\Rules\PathExistsInDiskRule;

class UploadRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'disk' => ['required', 'string', new DiskExistsRule()],
            'path' => ['required', 'string', new PathExistsInDiskRule($this->get('disk'))],
            'file' => ['required', 'file'],
        ];
    }
}
