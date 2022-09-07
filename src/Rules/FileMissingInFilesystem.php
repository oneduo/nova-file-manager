<?php

declare(strict_types=1);

namespace BBSLab\NovaFileManager\Rules;

use BBSLab\NovaFileManager\Http\Requests\BaseRequest;
use Illuminate\Contracts\Validation\Rule;

class FileMissingInFilesystem implements Rule
{
    public function __construct(
        public BaseRequest $request,
    ) {
    }

    public function passes($attribute, $value): bool
    {
        /** @var \Illuminate\Http\UploadedFile $value */

        return $this->request
            ->manager()
            ->filesystem()
            ->missing($value->getClientOriginalName());
    }

    public function message(): string
    {
        return __('validation.exists');
    }
}
