<?php

declare(strict_types=1);

namespace Oneduo\NovaFileManager\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Support\Collection;
use InvalidArgumentException;
use Oneduo\NovaFileManager\Support\Asset;
use Oneduo\NovaFileManager\Casts\Asset as AssetCast;

class AssetCollection implements CastsAttributes
{
    /**
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $value
     * @return \Illuminate\Support\Collection<\Oneduo\NovaFileManager\Support\Asset>
     *
     * @throws \JsonException
     */
    public function get($model, string $key, $value, array $attributes): Collection
    {
        if ($value === null) {
            return collect();
        }

        if (is_string($value)) {
            $value = json_decode($value, true, 512, JSON_THROW_ON_ERROR);
        } elseif (is_a($value, \stdClass::class)) {
            $value = (array) $value;
        }

        if (AssetCast::shouldTransformToUrl()) {
            return collect($value)
                ->filter()
                ->map(function ($file) {
                    if (is_array($file)) {
                        return AssetCast::transformArrayToUrl($file);
                    } elseif (is_a($file, \stdClass::class)) {
                        return AssetCast::transformArrayToUrl(['disk' => $file->disk, 'path' => $file->path]);
                    } else {
                        throw new \Exception('Invalid value for asset cast.');
                    }
                });
        }

        return collect($value)
            ->filter()
            ->map(function ($file) {
                if (is_array($file)) {
                    return new Asset(...$file);
                } elseif (is_a($file, \stdClass::class)) {
                    $file = (array) $file;
                    return new Asset(...$file);
                } else {
                    throw new \Exception('Invalid value for asset cast.');
                }
            });
    }

    /**
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  \Illuminate\Support\Collection<\Oneduo\NovaFileManager\Support\Asset>  $value
     */
    public function set($model, string $key, $value, array $attributes): string
    {
        if ($value instanceof Collection) {
            return $value->toJson();
        }

        throw new InvalidArgumentException('Invalid value for asset cast.');
    }
}
