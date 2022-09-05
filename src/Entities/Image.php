<?php

declare(strict_types=1);

namespace BBSLab\NovaFileManager\Entities;

use BBSLab\NovaFileManager\Filesystem\Metadata\Factory;

class Image extends Entity
{
    public function meta(): array
    {
        $data = Factory::build($this->fileSystem)?->analyze($this->path);

        return [
            'type' => 'image',
            'width' => data_get($data, 'video.resolution_x'),
            'height' => data_get($data, 'video.resolution_y'),
            'aspectRatio' => data_get($data, 'video.pixel_aspect_ratio'),
        ];
    }
}
