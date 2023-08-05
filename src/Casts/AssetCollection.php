<?php

declare(strict_types=1);

namespace Oneduo\NovaFileManager\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Support\Collection;
use InvalidArgumentException;
use Oneduo\NovaFileManager\Support\Asset;

class AssetCollection implements CastsAttributes
{
    /**
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $key
     * @param  string  $value
     * @param  array  $attributes
     * @return \Illuminate\Support\Collection<\Oneduo\NovaFileManager\Support\Asset>
     *
     * @throws \JsonException
     */
    public function get($model, string $key, $value, array $attributes): Collection
    {
        if ($value === null) {
            return collect();
        }

        return collect(json_decode($value, true, 512, JSON_THROW_ON_ERROR))
            ->filter()
            ->map(fn (array $file) => new Asset(...$file));
    }

    /**
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $key
     * @param  \Illuminate\Support\Collection<\Oneduo\NovaFileManager\Support\Asset>  $value
     * @param  array  $attributes
     * @return string
     */
    public function set($model, string $key, $value, array $attributes): string
    {
        if ($value instanceof Collection) {
            return $value->toJson();
        }

        throw new InvalidArgumentException('Invalid value for asset cast.');
    }
}
