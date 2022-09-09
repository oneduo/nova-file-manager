<?php

declare(strict_types=1);

namespace BBSLab\NovaFileManager;

use BBSLab\NovaFileManager\Contracts\Support\InteractsWithFilesystem;
use Illuminate\Http\Request;
use Laravel\Nova\Menu\MenuSection;
use Laravel\Nova\Tool;

class NovaFileManager extends Tool implements InteractsWithFilesystem
{
    use Traits\Support\InteractsWithFilesystem;

    public function menu(Request $request): mixed
    {
        return MenuSection::make('File Manager')
            ->path('/nova-file-manager')
            ->icon('server');
    }
}
