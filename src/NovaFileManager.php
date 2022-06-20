<?php

declare(strict_types=1);

namespace BBSLab\NovaFileManager;

use Illuminate\Http\Request;
use Laravel\Nova\Menu\MenuSection;
use Laravel\Nova\Nova;
use Laravel\Nova\Tool;

class NovaFileManager extends Tool
{
    public function boot(): void
    {
        Nova::script('nova-file-manager', __DIR__.'/../dist/js/tool.js');
        Nova::style('nova-file-manager', __DIR__.'/../dist/css/tool.css');
    }

    public function menu(Request $request): mixed
    {
        return MenuSection::make('File Manager')
            ->path('/nova-file-manager')
            ->icon('server');
    }
}
