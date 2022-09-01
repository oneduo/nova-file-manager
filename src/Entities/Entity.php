<?php

declare(strict_types=1);

namespace BBSLab\NovaFileManager\Entities;

use BBSLab\NovaFileManager\Contracts\Entity as EntityContract;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Filesystem\AwsS3V3Adapter;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\UnableToRetrieveMetadata;

abstract class Entity implements Arrayable, EntityContract
{
    protected array $data = [];

    protected Filesystem $fileSystem;

    public function __construct(
        public string $disk,
        public string $path,
    ) {
        $this->fileSystem = Storage::disk($disk);
    }

    public static function make(string $disk, string $path): static
    {
        return new static(
            $disk,
            $path,
        );
    }

    public function toArray(): array
    {
        if (empty($this->data)) {
            $shouldAnalyze = config('nova-file-manager.enable_file_analysis');

            $this->data = array_merge([
                'id' => $this->id(),
                'name' => $this->name(),
                'path' => $this->path,
                'size' => $this->size(),
                'extension' => $this->extension(),
                'mime' => $this->mime(),
                'url' => $this->url(),
                'lastModifiedAt' => $this->lastModifiedAt(),
                'type' => $this->type(),
            ], ['meta' => $shouldAnalyze ? $this->meta() : []]);
        }

        return $this->data;
    }

    public function id(): string
    {
        return sha1($this->fileSystem->path($this->path));
    }

    public function name(): string
    {
        return pathinfo($this->path, PATHINFO_BASENAME);
    }

    public function size(): int|string
    {
        $value = $this->fileSystem->size($this->path);

        if (!config('nova-file-manager.human_readable_size')) {
            return $value;
        }

        $units = ['B', 'KB', 'MB', 'GB', 'TB', 'PB'];

        for ($i = 0; $value > 1024; $i++) {
            $value /= 1024;
        }

        return round($value, 2).' '.$units[$i];
    }

    public function extension(): string
    {
        return pathinfo($this->path, PATHINFO_EXTENSION);
    }

    public function mime(): string
    {
        try {
            $type = $this->fileSystem->mimeType($this->path);

            if ($type === false) {
                throw UnableToRetrieveMetadata::mimeType($this->path);
            }

            return $type;
        } catch (UnableToRetrieveMetadata $e) {
            report($e);

            return 'application/octet-stream';
        }
    }

    public function url(): string
    {
        $supportsSignedUrls = $this->fileSystem instanceof AwsS3V3Adapter;

        if ($supportsSignedUrls && config('nova-file-manager.url_signing.enabled')) {
            return $this->fileSystem->temporaryUrl(
                $this->path,
                $this->signedExpirationTime(),
            );
        }

        return $this->fileSystem->url($this->path);
    }

    public function exists(): bool
    {
        return $this->fileSystem->exists($this->path);
    }

    public function signedExpirationTime(): Carbon
    {
        return now()->add(
            config('nova-file-manager.url_signing.unit'),
            config('nova-file-manager.url_signing.value'),
        );
    }

    public function lastModifiedAt(): string
    {
        if (!config('nova-file-manager.human_readable_datetime')) {
            return $this->lastModifiedAtTimestamp()->toDateTimeString();
        }

        return $this->lastModifiedAtTimestamp()->diffForHumans();
    }

    public function lastModifiedAtTimestamp(): Carbon
    {
        return Carbon::createFromTimestamp($this->fileSystem->lastModified($this->path));
    }

    public function type(): string
    {
        return (string) str($this->mime())->before('/');
    }

    abstract public function meta(): array;
}
