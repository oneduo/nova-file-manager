<?php

declare(strict_types=1);

namespace BBSLab\NovaFileManager\Rules;

use BBSLab\NovaFileManager\Http\Requests\BaseRequest;
use Illuminate\Contracts\Validation\Rule;

class ExistsInFilesystem implements Rule
{
    public ?string $path = null;

    public function __construct(public BaseRequest $request)
    {
    }

    public function passes($attribute, $value): bool
    {
        $this->path = $value;

        return empty($value)
            || $value === '/'
            || $this->request->manager()->filesystem()->exists($value);
    }

    public function message(): string
    {
        return __('nova-file-manager::validation.path.missing', ['path' => $this->path]);
    }
}
