<?php

declare(strict_types=1);

namespace Oneduo\NovaFileManager\Rules;

use Illuminate\Contracts\Validation\Rule;

class DiskExistsRule implements Rule
{
    public ?string $disk = null;

    public function passes($attribute, $value): bool
    {
        $this->disk = $value;

        $inFileManagerAvailableDisks = in_array($this->disk, config('nova-file-manager.available_disks'), true);

        $inFilesystemsDisks = array_key_exists($this->disk, config('filesystems.disks'));

        return $this->disk === null || ($inFileManagerAvailableDisks && $inFilesystemsDisks);
    }

    public function message(): string
    {
        return __('nova-file-manager::validation.disk.missing', ['disk' => $this->disk]);
    }
}
