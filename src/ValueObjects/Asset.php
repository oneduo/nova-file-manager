<?php

declare(strict_types=1);

namespace BBSLab\NovaFileManager\ValueObjects;

use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Traits\ForwardsCalls;

/**
 * @mixin \Illuminate\Contracts\Filesystem\Filesystem
 */
class Asset implements Arrayable, \JsonSerializable
{
    use ForwardsCalls;

    protected ?Filesystem $filesystem = null;

    public function __construct(public string $disk, public string $path)
    {
    }

    public function __call(string $name, array $arguments)
    {
        return $this->forwardCallTo($this->filesystem(), $name, $arguments);
    }

    public function filesystem(): Filesystem
    {
        if (!$this->filesystem) {
            $this->filesystem = Storage::disk($this->disk);
        }

        return $this->filesystem;
    }

    public function toArray(): array
    {
        return [
            'disk' => $this->disk,
            'path' => $this->path,
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
