<?php

declare(strict_types=1);

namespace Oneduo\NovaFileManager\Rules;

use Illuminate\Contracts\Validation\Rule;
use Oneduo\NovaFileManager\Contracts\Services\FileManagerContract;

class DiskExistsRule implements Rule
{
    public ?string $disk = null;

    public function passes($attribute, $value): bool
    {
        $this->disk = $value;

        $inFileManagerAvailableDisks = in_array($this->disk, array_merge(config('nova-file-manager.available_disks'), ['default']), true);

        $inFilesystemsDisks = array_key_exists($this->disk, array_merge(config('filesystems.disks'), ['default' => '']));

        return $this->disk === null || ($inFileManagerAvailableDisks && $inFilesystemsDisks);
    }

    public function message(): string
    {
        return __('nova-file-manager::validation.disk.missing', ['disk' => $this->disk]);
    }
}
