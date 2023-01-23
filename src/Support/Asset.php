<?php

declare(strict_types=1);

namespace Oneduo\NovaFileManager\Support;

use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Traits\ForwardsCalls;
use JsonSerializable;

/**
 * @mixin \Illuminate\Contracts\Filesystem\Filesystem
 */
class Asset implements Arrayable, JsonSerializable
{
    use ForwardsCalls;

    public function __construct(
        public string $disk,
        public string $path,
        public ?Filesystem $filesystem = null,
    ) {
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
    
    public function __toString(): string
    {
        return return json_encode($this, JSON_THROW_ON_ERROR);
    }
}
