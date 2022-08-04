<?php

declare(strict_types=1);

namespace BBSLab\NovaFileManager;

use BBSLab\NovaFileManager\Services\FileManagerService;
use Closure;
use Illuminate\Database\Eloquent\Model;
use JsonException;
use Laravel\Nova\Fields\Field;
use Laravel\Nova\Fields\PresentsImages;
use Laravel\Nova\Http\Requests\NovaRequest;

class FileManager extends Field
{
    use PresentsImages;

    public $component = 'nova-file-manager-field';

    public string $diskColumn;

    public Closure $storageCallback;

    public bool $copyable = false;

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
                Model $model,
                string $attribute,
                string $requestAttribute
            ) {
            $value = $request->input($requestAttribute);

            try {
                $payload = json_decode($value ?? '', true, 512, JSON_THROW_ON_ERROR);
            } catch (JsonException) {
                $payload = [];
            }

            $values = [
                $attribute => $payload['path'] ?? null,
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

    /**
     * @throws \JsonException
     */
    public function resolve($resource, $attribute = null): void
    {
        $attribute ??= $this->attribute;

        if (($path = $resource->{$attribute}) === null) {
            $this->value = null;

            return;
        }

        $manager = FileManagerService::make();

        if (isset($this->diskColumn)) {
            $disk = $resource->{$this->diskColumn};
        }

        if (isset($disk)) {
            $manager->disk($disk);
        }

        /** @var \BBSLab\NovaFileManager\Entities\Entity $entity */
        $entity = $manager->makeEntity($path);

        if (!$this->resolveCallback) {
            $this->value = [
                'disk' => $entity->disk,
                'path' => $entity->path,
                'file' => $entity->toArray(),
            ];
        } elseif (is_callable($this->resolveCallback)) {
            tap($this->resolveAttribute($resource, $attribute), function ($value) use ($resource, $attribute) {
                $this->value = call_user_func($this->resolveCallback, $value, $resource, $attribute);
            });
        }
    }

    public function jsonSerialize(): array
    {
        return array_merge(
            parent::jsonSerialize(),
            $this->imageAttributes(),
            [
                'copyable' => $this->copyable,
            ]
        );
    }
}
