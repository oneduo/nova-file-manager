<?php

declare(strict_types=1);

namespace Oneduo\NovaFileManager\Http\Requests;

use Oneduo\NovaFileManager\Rules\DiskExistsRule;
use Oneduo\NovaFileManager\Rules\ExistsInFilesystem;

/**
 * @property-read string[] $paths
 */
class DownloadFileRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return $this->canDownloadFile();
    }

    public function rules(): array
    {
        return [
            'disk' => ['sometimes', 'string', new DiskExistsRule()],
            'path' => ['required', 'string', new ExistsInFilesystem($this)],
        ];
    }
}
