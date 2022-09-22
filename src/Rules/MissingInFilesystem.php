<?php

declare(strict_types=1);

namespace Oneduo\NovaFileManager\Rules;

use Oneduo\NovaFileManager\Http\Requests\BaseRequest;
use Illuminate\Contracts\Validation\Rule;

class MissingInFilesystem implements Rule
{
    public ?string $path = null;

    public function __construct(public BaseRequest $request)
    {
    }

    public function passes($attribute, $value): bool
    {
        $this->path = $value;

        return $this->request
            ->manager()
            ->filesystem()
            ->missing($value);
    }

    public function message(): string
    {
        return __('nova-file-manager::validation.path.exists', ['path' => $this->path]);
    }
}
