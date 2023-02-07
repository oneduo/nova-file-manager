<?php

declare(strict_types=1);

namespace Oneduo\NovaFileManager;

use Closure;
use JsonException;
use Laravel\Nova\Contracts\Cover;
use Laravel\Nova\Fields\Field;
use Laravel\Nova\Fields\HasThumbnail;
use Laravel\Nova\Fields\PresentsImages;
use Laravel\Nova\Fields\SupportsDependentFields;
use Laravel\Nova\Http\Requests\NovaRequest;
use Oneduo\NovaFileManager\Contracts\Services\FileManagerContract;
use Oneduo\NovaFileManager\Contracts\Support\InteractsWithFilesystem as InteractsWithFilesystemContract;
use Oneduo\NovaFileManager\Support\Asset;
use Oneduo\NovaFileManager\Traits\Support\InteractsWithFilesystem;
use Illuminate\Support\Facades\Storage;
use stdClass;

class FileManager extends Field implements InteractsWithFilesystemContract, Cover
{
    use SupportsDependentFields;
    use InteractsWithFilesystem;
    use HasThumbnail;
    use PresentsImages;

    public $component = 'nova-file-manager-field';

    public bool $multiple = false;

    public ?int $limit = null;

    public bool $asHtml = false;

    public Closure $storageCallback;

    public static array $wrappers = [];

    public ?string $wrapper = null;

    public function __construct($name, $attribute = null, Closure $storageCallback = null)
    {
        parent::__construct($name, $attribute);

        $this->prepareStorageCallback($storageCallback);

        $this->thumbnail(function ($value, $disk) {
            return $value ? Storage::disk($disk)->url($value) : null;
        });
    }

    public function resolveThumbnailUrl()
    {
        $value = data_get($this->value, '0.path');
        $disk = data_get($this->value, '0.disk') ?? config('nova.storage_disk');

        return is_callable($this->thumbnailUrlCallback)
                    ? call_user_func($this->thumbnailUrlCallback, $value, $disk, $this->resource)
                    : null;
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

    public function asHtml(): static
    {
        $this->asHtml = true;

        return $this;
    }

    public function wrapper(string $name): static
    {
        $this->wrapper = $name;

        return $this;
    }

    /**
     * @throws \JsonException
     */
    protected function fillAttribute(NovaRequest $request, $requestAttribute, $model, $attribute)
    {
        $this->applyWrapper();

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
            $value = (array)$value;
        }

        if (is_array($value)) {
            if ($this->multiple) {
                $value = collect($value)->map(fn(array|object $asset) => new Asset(...(array)$asset));
            } else {
                $value = collect([new Asset(...$value)]);
            }
        }

        $this->applyWrapper();

        return $value
            ->map(function (Asset $asset) {
                $disk = $this->resolveFilesystem(app(NovaRequest::class)) ?? $asset->disk;

                $manager = app(FileManagerContract::class, ['disk' => $disk]);

                if ($this->hasUrlResolver()) {
                    $manager->resolveUrlUsing($this->getUrlResolver());
                }

                return $manager->makeEntity($asset->path, $asset->disk);
            })
            ->toArray();
    }

    public static function registerWrapper(string $name, Closure $callback): void
    {
        static::$wrappers[$name] = $callback;
    }

    public static function forWrapper(string $name): ?static
    {
        if (!$callback = (static::$wrappers[$name] ?? null)) {
            return null;
        }

        return $callback(static::make('wrapped'));
    }

    public function applyWrapper(): static
    {
        if (empty($this->wrapper)) {
            return $this;
        }

        if (!$wrapper = static::forWrapper($this->wrapper)) {
            return $this;
        }

        $this->prepareStorageCallback($wrapper->storageCallback);

        $this->multiple = $wrapper->multiple;
        $this->limit = $wrapper->limit;
        $this->asHtml = $wrapper->asHtml;

        $this->merge($wrapper);

        return $this;
    }

    public function jsonSerialize(): array
    {
        $this->applyWrapper();

        return array_merge(
            parent::jsonSerialize(),
            [
                'multiple' => $this->multiple,
                'limit' => $this->multiple ? $this->limit : 1,
                'asHtml' => $this->asHtml,
                'wrapper' => $this->wrapper,
            ],
            $this->options(),
        );
    }
}
