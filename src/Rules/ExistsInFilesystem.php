<?php

declare(strict_types=1);

namespace BBSLab\NovaFileManager\Rules;

use BBSLab\NovaFileManager\Http\Requests\BaseRequest;
use Illuminate\Contracts\Validation\Rule;

class ExistsInFilesystem implements Rule
{
    public function __construct(
        public BaseRequest $request,
    ) {}

    public function passes($attribute, $value): bool
    {
        return empty($value)
            || $value === '/'
            || $this->request->manager()->filesystem()->exists($value);
    }

    public function message(): string
    {
        return __('validation.exists');
    }
}
