<?php

declare(strict_types=1);

namespace BBSLab\NovaFileManager\Rules;

use BBSLab\NovaFileManager\Http\Requests\BaseRequest;
use Illuminate\Contracts\Validation\Rule;

class FileMissingInFilesystem implements Rule
{
    public ?string $path = null;

    public function __construct(public BaseRequest $request)
    {
    }

    /**
     * @param $attribute
     * @param  \Illuminate\Http\UploadedFile  $value
     * @return bool
     */
    public function passes($attribute, $value): bool
    {
        $this->path = $value->getClientOriginalName();

        return $this->request
            ->manager()
            ->filesystem()
            ->missing($value->getClientOriginalName());
    }

    public function message(): string
    {
        return __('nova-file-manager::validation.path.missing', ['path' => $this->path]);
    }
}
