<?php

namespace Workbench\App\Nova\Repeater;

use Laravel\Nova\Fields\Repeater\Repeatable;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use Oneduo\NovaFileManager\FileManager;

class UserInfoItem extends Repeatable
{
    public function fields(NovaRequest $request): array
    {
        return [
            Text::make('Card'),
            FileManager::make('Image')->wrapper('repeater'),
        ];
    }
}