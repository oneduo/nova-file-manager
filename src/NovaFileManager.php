<?php

declare(strict_types=1);

namespace Oneduo\NovaFileManager;

use Illuminate\Http\Request;
use Laravel\Nova\Menu\MenuSection;
use Laravel\Nova\Tool;
use Oneduo\NovaFileManager\Contracts\Support\InteractsWithFilesystem;

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
