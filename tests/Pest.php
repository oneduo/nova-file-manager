<?php

declare(strict_types=1);

use BBSLab\NovaFileManager\NovaFileManager;
use BBSLab\NovaFileManager\Tests\DuskTestCase;
use BBSLab\NovaFileManager\Tests\TestCase;
use BBSLab\NovaFileManager\Tests\Traits\FileConcerns;
use BBSLab\NovaFileManager\Tests\Traits\FolderConcerns;
use Laravel\Nova\Nova;

uses(TestCase::class)->in('Feature');

uses()->beforeEach(function () {
    Nova::$tools = [
        NovaFileManager::make(),
    ];
})->in('Feature');

uses(FolderConcerns::class)->in('Feature/Directory');
uses(FileConcerns::class)->in('Feature/File');

uses(DuskTestCase::class)->in('Browser');

uses()->group('integration')->in('Feature');
uses()->group('browser')->in('Browser');