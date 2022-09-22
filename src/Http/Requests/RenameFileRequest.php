<?php

declare(strict_types=1);

namespace Oneduo\NovaFileManager\Http\Requests;

use Oneduo\NovaFileManager\Rules\DiskExistsRule;
use Oneduo\NovaFileManager\Rules\ExistsInFilesystem;
use Oneduo\NovaFileManager\Rules\MissingInFilesystem;

/**
 * @property-read string $from
 * @property-read string $to
 */
class RenameFileRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return $this->canRenameFile();
    }

    public function rules(): array
    {
        return [
            'disk' => ['sometimes', 'string', new DiskExistsRule()],
            'from' => ['required', 'string', new ExistsInFilesystem($this)],
            'to' => ['required', 'string', new MissingInFilesystem($this)],
        ];
    }
}
