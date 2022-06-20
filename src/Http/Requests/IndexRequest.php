<?php

declare(strict_types=1);

namespace BBSLab\NovaFileManager\Http\Requests;

use BBSLab\NovaFileManager\Rules\DiskExistsRule;
use BBSLab\NovaFileManager\Rules\PathExistsInDiskRule;

class IndexRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'disk' => ['sometimes', 'string', new DiskExistsRule()],
            'path' => ['sometimes', 'string', new PathExistsInDiskRule($this->get('disk'))],
            'page' => ['sometimes', 'numeric', 'min:1'],
            'perPage' => ['sometimes', 'numeric', 'min:1'],
            'search' => ['nullable', 'string'],
        ];
    }
}
