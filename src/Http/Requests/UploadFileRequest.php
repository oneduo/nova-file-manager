<?php

declare(strict_types=1);

namespace BBSLab\NovaFileManager\Http\Requests;

use BBSLab\NovaFileManager\Filesystem\Support\GetID3;
use BBSLab\NovaFileManager\Rules\DiskExistsRule;
use BBSLab\NovaFileManager\Rules\ExistsInFilesystem;
use BBSLab\NovaFileManager\Rules\FileMissingInFilesystem;
use Illuminate\Http\UploadedFile;

/**
 * @property-read string|null $disk
 * @property-read string $path
 * @property-read \Illuminate\Http\UploadedFile $file
 */
class UploadFileRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return $this->canUploadFile();
    }

    public function rules(): array
    {
        return [
            'disk' => ['sometimes', 'string', new DiskExistsRule()],
            'path' => ['required', 'string', new ExistsInFilesystem($this)],
            'file' => array_merge(
                ['required', 'file', new FileMissingInFilesystem($this)],
                $this->element()->getUploadRules(),
            ),
        ];
    }

    public function validateUpload(?UploadedFile $file = null, bool $saving = false): bool
    {
        if (!$this->element()->hasUploadValidator()) {
            return true;
        }

        $file ??= $this->file('file');

        return call_user_func(
            $this->element()->getUploadValidator(),
            $this,
            $file,
            (new GetID3())->analyze($file->path()),
            $saving,
        );
    }
}
