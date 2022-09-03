<?php

declare(strict_types=1);

use BBSLab\NovaFileManager\NovaFileManager;
use BBSLab\NovaFileManager\Tests\TestCase;
use BBSLab\NovaFileManager\Tests\Traits\FileConcerns;
use BBSLab\NovaFileManager\Tests\Traits\FolderConcerns;
use Laravel\Nova\Nova;

uses(TestCase::class)->in(__DIR__);

uses()->beforeEach(function () {
    Nova::$tools = [
        NovaFileManager::make(),
    ];
})->in(__DIR__);

uses(FolderConcerns::class)->in('Directory');
uses(FileConcerns::class)->in('File');
