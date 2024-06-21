<?php

declare(strict_types=1);

namespace Workbench\App\Nova\Flexible\Layouts;

use Laravel\Nova\Fields\Text;
use Oneduo\NovaFileManager\FileManager;
use Whitecube\NovaFlexibleContent\Layouts\Layout;

class ImageAndCaptionLayout extends Layout
{
    protected $name = 'imageandcaptionlayout';

    protected $title = 'ImageAndCaptionLayout';

    public function fields(): array
    {
        return [
            FileManager::make('Image', 'image')->nullable(),
            Text::make('Caption', 'caption')->nullable(),
        ];
    }
}
