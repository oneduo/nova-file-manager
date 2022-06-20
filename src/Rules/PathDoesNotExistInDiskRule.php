<?php

declare(strict_types=1);

namespace BBSLab\NovaFileManager\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Storage;

class PathDoesNotExistInDiskRule implements Rule
{
    public function __construct(
        public ?string $disk = null
    ) {
    }

    public function passes($attribute, $value): bool
    {
        return $this->disk === null || !Storage::disk($this->disk)->exists($value);
    }

    public function message(): string
    {
        return __('validation.exists');
    }
}