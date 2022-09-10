<?php

declare(strict_types=1);

namespace BBSLab\NovaFileManager\Rules;

use BBSLab\NovaFileManager\Http\Requests\UploadFileRequest;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Str;

class FileMissingInFilesystem implements Rule
{
    public ?string $path = null;

    public function __construct(public UploadFileRequest $request)
    {
    }

    /**
     * @param $attribute
     * @param  \Illuminate\Http\UploadedFile  $value
     * @return bool
     */
    public function passes($attribute, $value): bool
    {
        $this->path = Str::finish($this->request->path, '/').$value->getClientOriginalName();

        return $this->request
            ->manager()
            ->filesystem()
            ->missing($this->path);
    }

    public function message(): string
    {
        return __('nova-file-manager::validation.path.exists', ['path' => $this->path]);
    }
}
