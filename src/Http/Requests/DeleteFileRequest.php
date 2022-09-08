<?php

declare(strict_types=1);

namespace BBSLab\NovaFileManager\Http\Requests;

use BBSLab\NovaFileManager\Rules\DiskExistsRule;
use BBSLab\NovaFileManager\Rules\ExistsInFilesystem;

/**
 * @property-read string $path
 */
class DeleteFileRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return $this->canDeleteFile();
    }

    public function rules(): array
    {
        return [
            'disk' => ['sometimes', 'string', new DiskExistsRule()],
            'path' => ['required', 'string', new ExistsInFilesystem($this)],
        ];
    }
}
