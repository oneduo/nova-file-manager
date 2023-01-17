<?php

declare(strict_types=1);

namespace Oneduo\NovaFileManager\Http\Requests;

use Oneduo\NovaFileManager\Rules\DiskExistsRule;
use Oneduo\NovaFileManager\Rules\ExistsInFilesystem;

/**
 * @property-read string[] $paths
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
            'paths' => ['required', 'array'],
            'paths.*' => ['required', 'string', new ExistsInFilesystem($this)],
        ];
    }
}
