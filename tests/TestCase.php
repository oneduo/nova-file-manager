<?php

declare(strict_types=1);

namespace BBSLab\NovaFileManager\Tests;

use BBSLab\NovaFileManager\ToolServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            ToolServiceProvider::class,
        ];
    }
}
