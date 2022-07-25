<?php

declare(strict_types=1);

namespace BBSLab\NovaFileManager\Rules;

use Illuminate\Contracts\Validation\Rule;

class DiskExistsRule implements Rule
{
    public function passes($attribute, $value): bool
    {
        $inFileManagerAvailableDisks = in_array($value, config('nova-file-manager.available_disks'), true);

        $inFilesystemsDisks = array_key_exists($value, config('filesystems.disks'));

        return $value === null || ($inFileManagerAvailableDisks && $inFilesystemsDisks);
    }

    public function message(): string
    {
        return __('validation.exists');
    }
}
