<?php

declare(strict_types=1);

namespace BBSLab\NovaFileManager\Casts;

use BBSLab\NovaFileManager\ValueObjects\Asset as AssetValueObject;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use InvalidArgumentException;

class Asset implements CastsAttributes
{
    /**
     * @inheritDoc
     * @throws \JsonException
     */
    public function get($model, string $key, $value, array $attributes)
    {
        return new AssetValueObject(...json_decode($value, true, 512, JSON_THROW_ON_ERROR));
    }

    /**
     * @inheritDoc
     * @throws \JsonException
     */
    public function set($model, string $key, $value, array $attributes)
    {
        if ($value instanceof AssetValueObject) {
            return json_encode($value, JSON_THROW_ON_ERROR);
        }

        if(is_array($value)){
            return json_encode($value, JSON_THROW_ON_ERROR);
        }

        throw new InvalidArgumentException('Invalid value for asset cast.');
    }
}
