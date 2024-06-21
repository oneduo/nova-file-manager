<?php

declare(strict_types=1);

namespace Oneduo\NovaFileManager\Http\Requests;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Oneduo\NovaFileManager\Filesystem\Support\GetID3;
use Oneduo\NovaFileManager\Rules\DiskExistsRule;
use Oneduo\NovaFileManager\Rules\FileMissingInFilesystem;

/**
 * @property-read string|null $disk
 * @property-read string $path
 * @property-read \Illuminate\Http\UploadedFile $file
 */
class UploadFileRequest extends BaseRequest
{
    public function authorize(): bool
    {
        if (!$this->canUploadFile()) {
            return false;
        }

        $path = ltrim(dirname($this->input('resumableFilename')), '/.');

        if (!empty($path) && !$this->canCreateFolder()) {
            return false;
        }

        return true;
    }

    public function authorizationActionAttribute(?string $class = null): string
    {
        if (!$this->canUploadFile()) {
            return parent::authorizationActionAttribute();
        }

        return parent::authorizationActionAttribute(CreateFolderRequest::class);
    }

    public function rules(): array
    {
        $uploadReplaceExisting = $this->element()->resolveUploadReplaceExisting($this);

        $rules = ['required', 'file'];

        if (!$uploadReplaceExisting) {
            $rules[] = new FileMissingInFilesystem($this);
        }

        return [
            'disk' => ['sometimes', 'string', new DiskExistsRule()],
            'path' => ['required', 'string'],
            'file' => array_merge(
                $rules,
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

    public function filePath(): string
    {
        $path = implode('/', array_filter([
            Str::finish($this->path, '/'),
            ltrim($this->input('resumableFilename'), '/'),
        ]));

        return str_replace('//', '/', $path);
    }
}
