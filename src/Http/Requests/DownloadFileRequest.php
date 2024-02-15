<?php

declare(strict_types=1);

namespace Oneduo\NovaFileManager\Http\Requests;

use Oneduo\NovaFileManager\Rules\DiskExistsRule;
use Oneduo\NovaFileManager\Rules\ExistsInFilesystem;

/**
 * @property-read string $path
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
            'path' => ['required', 'string', new ExistsInFilesystem($this)],
        ];
    }
}
