<?php

declare(strict_types=1);

namespace BBSLab\NovaFileManager;

use BBSLab\NovaFileManager\Contracts\InteractsWithFilesystem as InteractsWithFilesystemContract;
use BBSLab\NovaFileManager\Contracts\Services\FileManagerContract;
use BBSLab\NovaFileManager\ValueObjects\Asset;
use Closure;
use JsonException;
use Laravel\Nova\Fields\Field;
use Laravel\Nova\Fields\PresentsImages;
use Laravel\Nova\Http\Requests\NovaRequest;

class FileManager extends Field implements InteractsWithFilesystemContract
{
    use InteractsWithFilesystem;
    use PresentsImages;

    public $component = 'nova-file-manager-field';

    public string $diskColumn;

    public bool $copyable = false;

    public bool $multiple = false;

    public ?int $limit = null;

    public Closure $storageCallback;

    public function __construct($name, $attribute = null, Closure $storageCallback = null)
    {
        parent::__construct($name, $attribute);

        $this->prepareStorageCallback($storageCallback);
    }

    public function copyable(): static
    {
        $this->copyable = true;

        return $this;
    }

    public function storeDisk(string $column): static
    {
        $this->diskColumn = $column;

        return $this;
    }

    public function multiple(bool $multiple = true): static
    {
        $this->multiple = $multiple;

        return $this;
    }

    public function limit(?int $limit = null): static
    {
        $this->limit = $limit;

        return $this;
    }

    /**
     * @throws \JsonException
     */
    protected function fillAttribute(NovaRequest $request, $requestAttribute, $model, $attribute)
    {
        $result = call_user_func(
            $this->storageCallback,
            $request,
            $model,
            $attribute,
            $requestAttribute
        );

        if ($result === true) {
            return;
        }

        if ($result instanceof Closure) {
            return $result;
        }

        if (!is_array($result)) {
            return $model->{$attribute} = $result;
        }

        foreach ($result as $key => $value) {
            $model->{$key} = $value;
        }
    }

    protected function prepareStorageCallback(Closure $storageCallback = null): void
    {
        $this->storageCallback = $storageCallback ?? function (
            NovaRequest $request,
            $model,
            string $attribute,
            string $requestAttribute
        ) {
            $value = $request->input($requestAttribute);

            try {
                $payload = json_decode($value ?? '', true, 512, JSON_THROW_ON_ERROR);
            } catch (JsonException) {
                $payload = [];
            }

            $files = collect($payload);

            if ($this->multiple) {
                $value = collect($files)->map(fn(array $file) => new Asset(...$file));
            } else {
                $value = $files->isNotEmpty() ? new Asset(...$files->first()) : null;
            }

            $values = [
                $attribute => $value,
            ];

            return $this->mergeExtraStorageColumns($payload, $values);
        };
    }

    protected function mergeExtraStorageColumns(array $payload, array $attributes): array
    {
        if (isset($this->diskColumn)) {
            $attributes[$this->diskColumn] = $payload['disk'] ?? null;
        }

        return $attributes;
    }

    protected function resolveAttribute($resource, $attribute = null): ?array
    {
        if (!$value = parent::resolveAttribute($resource, $attribute)) {
            return null;
        }

        if ($value instanceof Asset) {
            $value = collect($value);
        }

        return $value
            ->map(function (Asset $asset) {
                $manager = $this->resolveFilesystem(app(NovaRequest::class))
                    ?? app(FileManagerContract::class, ['disk' => $asset->disk]);

                return $manager->makeEntity($asset->path, $asset->disk);
            })
            ->toArray();
    }

    public function jsonSerialize(): array
    {
        return array_merge(
            parent::jsonSerialize(),
            $this->imageAttributes(),
            [
                'copyable' => $this->copyable,
                'multiple' => $this->multiple,
                'limit' => $this->multiple ? $this->limit : 1,
            ],
            $this->options(),
        );
    }
}
