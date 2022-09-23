<?php

declare(strict_types=1);

namespace Oneduo\NovaFileManager\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use InvalidArgumentException;
use Oneduo\NovaFileManager\Support\Asset as AssetObject;

class Asset implements CastsAttributes
{
    /**
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $key
     * @param  string|null  $value
     * @param  array  $attributes
     * @return \Oneduo\NovaFileManager\Support\Asset|null
     *
     * @throws \JsonException
     */
    public function get($model, string $key, $value, array $attributes): ?AssetObject
    {
        if ($value === null) {
            return null;
        }

        return new AssetObject(...json_decode($value, true, 512, JSON_THROW_ON_ERROR));
    }

    /**
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $key
     * @param  array|AssetObject|null  $value
     * @param  array  $attributes
     * @return string|null
     *
     * @throws \JsonException
     */
    public function set($model, string $key, $value, array $attributes): ?string
    {
        if ($value === null) {
            return null;
        }

        if (is_array($value) || $value instanceof AssetObject) {
            return json_encode($value, JSON_THROW_ON_ERROR);
        }

        throw new InvalidArgumentException('Invalid value for Asset cast');
    }
}
