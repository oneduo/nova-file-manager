<?php

declare(strict_types=1);

namespace BBSLab\NovaFileManager\Entities;

use BBSLab\NovaFileManager\Filesystem\Metadata\Factory;

class Video extends Entity
{
    public function meta(): array
    {
        $data = Factory::build($this->manager->filesystem())?->analyze($this->path);

        return [
            'type' => 'video',
            'width' => data_get($data, 'video.resolution_x'),
            'height' => data_get($data, 'video.resolution_y'),
        ];
    }
}
