<?php

declare(strict_types=1);

namespace BBSLab\NovaFileManager\Http\Requests;

use BBSLab\NovaFileManager\Rules\DiskExistsRule;
use BBSLab\NovaFileManager\Rules\ExistsInFilesystem;
use BBSLab\NovaFileManager\Rules\MissingInFilesystem;

/**
 * @property-read string $oldPath
 * @property-read string $newPath
 */
class RenameFolderRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'disk' => ['sometimes', 'string', new DiskExistsRule()],
            'oldPath' => ['required', 'string', new ExistsInFilesystem($this)],
            'newPath' => ['required', 'string', new MissingInFilesystem($this)],
        ];
    }
}
