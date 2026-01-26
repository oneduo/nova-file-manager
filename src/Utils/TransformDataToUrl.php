<?php

namespace Oneduo\NovaFileManager\Utils;

use Illuminate\Support\Facades\Storage;

class TransformDataToUrl
{
    /**
     * @param array $data
     */
    public function __construct(protected array $data) {}

    /**
     * @return string
     */
    public function __toString(): string
    {
        try {
            return Storage::disk($this->data['disk'])->url($this->data['path']);
        } catch (\Exception $e) {
            return '';
        }
    }
}