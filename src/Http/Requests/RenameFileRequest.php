<?php

declare(strict_types=1);

namespace BBSLab\NovaFileManager\Http\Requests;

use BBSLab\NovaFileManager\Rules\DiskExistsRule;
use BBSLab\NovaFileManager\Rules\PathDoesNotExistInDiskRule;
use BBSLab\NovaFileManager\Rules\PathExistsInDiskRule;

class RenameFileRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'disk' => ['sometimes', 'string', new DiskExistsRule()],
            'oldPath' => ['required', 'string', new PathExistsInDiskRule($this->get('disk'))],
            'newPath' => ['required', 'string', new PathDoesNotExistInDiskRule($this->get('disk'))],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}