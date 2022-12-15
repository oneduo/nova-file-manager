<?php

declare(strict_types=1);

namespace Oneduo\NovaFileManager;

use Closure;
use JsonException;
use Laravel\Nova\Fields\Field;
use Laravel\Nova\Http\Requests\NovaRequest;
use Oneduo\NovaFileManager\Contracts\Services\FileManagerContract;
use Oneduo\NovaFileManager\Contracts\Support\InteractsWithFilesystem;
use Oneduo\NovaFileManager\Support\Asset;
use stdClass;

class FileManager extends Field implements InteractsWithFilesystem
{
    use Traits\Support\InteractsWithFilesystem;

    public $component = 'nova-file-manager-field';

    public bool $multiple = false;

    public ?int $limit = null;

    /**
     * Indicates if the field value should be displayed as HTML.
     *
     * @var bool
     */
    public bool $asHtml = false;

    public Closure $storageCallback;

    public function __construct($name, $attribute = null, Closure $storageCallback = null)
    {
        parent::__construct($name, $attribute);

        $this->prepareStorageCallback($storageCallback);
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
     * Display the field as raw HTML using Vue.
     *
     * @return $this
     */
    public function asHtml(): static
    {
        $this->asHtml = true;

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
                $value = collect($files)->map(fn (array $file) => new Asset(...$file));
            } else {
                $value = $files->isNotEmpty() ? new Asset(...$files->first()) : null;
            }

            return [$attribute => $value];
        };
    }

    protected function resolveAttribute($resource, $attribute = null): ?array
    {
        if (!$value = parent::resolveAttribute($resource, $attribute)) {
            return null;
        }

        if ($value instanceof Asset) {
            $value = collect([$value]);
        }

        if ($value instanceof stdClass) {
            $value = (array) $value;
        }

        if (is_array($value)) {
            if ($this->multiple) {
                $value = collect($value)->map(fn (array|object $asset) => new Asset(... (array) $asset));
            } else {
                $value = collect([new Asset(...$value)]);
            }
        }

        return $value
            ->map(function (Asset $asset) {
                $disk = $this->resolveFilesystem(app(NovaRequest::class)) ?? $asset->disk;

                /** @var \Oneduo\NovaFileManager\Services\FileManagerService $manager */
                $manager = app(FileManagerContract::class, ['disk' => $disk]);

                if ($this->hasUrlResolver()) {
                    $manager->resolveUrlUsing($this->getUrlResolver());
                }

                return $manager->makeEntity($asset->path, $asset->disk);
            })
            ->toArray();
    }

    public function jsonSerialize(): array
    {
        return array_merge(
            parent::jsonSerialize(),
            [
                'multiple' => $this->multiple,
                'limit' => $this->multiple ? $this->limit : 1,
                'asHtml' => $this->asHtml,
            ],
            $this->options(),
        );
    }
}
