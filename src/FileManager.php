<?php

declare(strict_types=1);

namespace BBSLab\NovaFileManager;

use BBSLab\NovaFileManager\Services\FileManagerService;
use Closure;
use JsonException;
use Laravel\Nova\Fields\Field;
use Laravel\Nova\Fields\PresentsImages;
use Laravel\Nova\Http\Requests\NovaRequest;

class FileManager extends Field
{
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

    public function asJson(string $column)
    {
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

            $files = collect($payload['files'] ?? []);

            $value = match ($files->count()) {
                0 => null,
                1 => $files->first()['path'],
                default => $files->pluck('path')->toArray(),
            };

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

        $manager = FileManagerService::make();

        if (isset($this->diskColumn)) {
            $disk = parent::resolveAttribute($resource, $this->diskColumn);
        }

        if (isset($disk)) {
            $manager->disk($disk);
        }

        $entities = collect();

        if (is_string($value)) {
            if (empty($value)) {
                return null;
            }

            $entity = $manager->makeEntity($value);

            if ($entity->exists()) {
                $entities->push($entity);
            }
        }

        if (is_iterable($value)) {
            foreach ($value as $file) {
                $entity = $manager->makeEntity($file);

                if ($entity->exists()) {
                    $entities->push($entity);
                }
            }
        }

        return [
            'disk' => $manager->disk,
            'files' => $entities->toArray(),
        ];
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
            ]
        );
    }
}
