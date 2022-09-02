<?php

declare(strict_types=1);

namespace BBSLab\NovaFileManager\Entities;

use BBSLab\NovaFileManager\Contracts\Entities\Entity as EntityContract;
use Illuminate\Contracts\fileSystem\Filesystem;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\fileSystem\AwsS3V3Adapter;
use Illuminate\Support\Carbon;
use League\Flysystem\UnableToRetrieveMetadata;

abstract class Entity implements Arrayable, EntityContract
{
    protected array $data = [];

    public function __construct(
        public Filesystem $fileSystem,
        public string $path,
    ) {}

    /**
     * Static helper
     *
     * @param  \Illuminate\Contracts\Filesystem\Filesystem  $fileSystem
     * @param  string  $path
     *
     * @return static
     */
    public static function make(Filesystem $fileSystem, string $path): static
    {
        return new static(
            $fileSystem,
            $path,
        );
    }

    /**
     * Return the entity's data as array
     *
     * @return array
     */
    public function toArray(): array
    {
        if (empty($this->data)) {
            $shouldAnalyze = config('nova-file-manager.file_analysis.enable');

            if ($this->fileSystem->exists($this->path)) {
                $this->data = array_merge(
                    [
                        'id' => $this->id(),
                        'name' => $this->name(),
                        'path' => $this->path,
                        'size' => $this->size(),
                        'extension' => $this->extension(),
                        'mime' => $this->mime(),
                        'url' => $this->url(),
                        'lastModifiedAt' => $this->lastModifiedAt(),
                        'type' => $this->type(),
                        'exists' => true,
                    ],
                    [
                        'meta' => $shouldAnalyze ? $this->meta() : [],
                    ],
                );
            } else {
                $this->data = array_merge([
                    'id' => $this->id(),
                    'path' => $this->path,
                    'exists' => false,
                ]);
            }
        }

        return $this->data;
    }

    /**
     * Generate a unique identifier for the entity
     *
     * @return string
     */
    public function id(): string
    {
        return sha1($this->fileSystem->path($this->path));
    }

    /**
     * Get the name of the entity
     *
     * @return string
     */
    public function name(): string
    {
        return pathinfo($this->path, PATHINFO_BASENAME);
    }

    /**
     * Compute the size of the entity
     *
     * @return int|string
     */
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

    /**
     * Get the file extension of the entity
     *
     * @return string
     */
    public function extension(): string
    {
        return pathinfo($this->path, PATHINFO_EXTENSION);
    }

    /**
     * Get the mime type of the entity
     *
     * @return string
     */
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

    /**
     * Build an url for the entity based on the disk
     *
     * @return string
     */
    public function url(): string
    {
        $supportsSignedUrls = $this->fileSystem instanceof AwsS3V3Adapter;

        // signed urls are only supported on S3 disks
        if ($supportsSignedUrls && config('nova-file-manager.url_signing.enabled')) {
            return $this->fileSystem->temporaryUrl(
                $this->path,
                $this->signedExpirationTime(),
            );
        }

        // we fallback to the regular url builder
        return $this->fileSystem->url($this->path);
    }

    /**
     * Get the expiration time from the user defined config
     *
     * @return \Illuminate\Support\Carbon
     */
    public function signedExpirationTime(): Carbon
    {
        return now()->add(
            config('nova-file-manager.url_signing.unit'),
            config('nova-file-manager.url_signing.value'),
        );
    }

    /**
     * Get the last modified time of the entity as string
     *
     * @return string
     */
    public function lastModifiedAt(): string
    {
        if (!config('nova-file-manager.human_readable_datetime')) {
            return $this->lastModifiedAtTimestamp()->toDateTimeString();
        }

        return $this->lastModifiedAtTimestamp()->diffForHumans();
    }

    /**
     * Get the last modified time of the entity as a Carbon instance
     *
     * @return \Illuminate\Support\Carbon
     */
    public function lastModifiedAtTimestamp(): Carbon
    {
        return Carbon::createFromTimestamp($this->fileSystem->lastModified($this->path));
    }

    /**
     * Define the type of the entity
     *
     * @return string
     */
    public function type(): string
    {
        return (string) str($this->mime())->before('/');
    }

    abstract public function meta(): array;
}
