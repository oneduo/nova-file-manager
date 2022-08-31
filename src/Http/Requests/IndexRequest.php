<?php

declare(strict_types=1);

namespace BBSLab\NovaFileManager\Http\Requests;

use BBSLab\NovaFileManager\Rules\DiskExistsRule;
use BBSLab\NovaFileManager\Rules\PathExistsInDiskRule;

/**
 * @property-read ?string $disk
 * @property-read string $path
 * @property-read ?int $page
 * @property-read ?int $perPage
 * @property-read ?string $search
 */
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
