<?php

declare(strict_types=1);

use Oneduo\NovaFileManager\Http\Controllers\DiskController;
use Oneduo\NovaFileManager\Http\Controllers\FileController;
use Oneduo\NovaFileManager\Http\Controllers\FolderController;
use Oneduo\NovaFileManager\Http\Controllers\IndexController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Tool API Routes
|--------------------------------------------------------------------------
|
| Here is where you may register API routes for your tool. These routes
| are loaded by the ServiceProvider of your tool. They are protected
| by your tool's "Authorize" middleware by default. Now, go build!
|
*/

Route::as('nova-file-manager.')->middleware('nova')->group(static function () {
    Route::get('/{resource?}', IndexController::class)->name('data');

    Route::prefix('disks')->as('disks.')->group(static function () {
        Route::get('available/{resource?}', [DiskController::class, 'available'])->name('available');
    });

    Route::prefix('files')->as('files.')->group(function () {
        Route::post('upload/{resource?}', [FileController::class, 'upload'])->name('upload');
        Route::post('rename/{resource?}', [FileController::class, 'rename'])->name('rename');
        Route::post('delete/{resource?}', [FileController::class, 'delete'])->name('delete');
        Route::post('unzip/{resource?}', [FileController::class, 'unzip'])->name('unzip');
    });

    Route::prefix('folders')->as('folders.')->group(function () {
        Route::post('create/{resource?}', [FolderController::class, 'create'])->name('create');
        Route::post('rename/{resource?}', [FolderController::class, 'rename'])->name('rename');
        Route::post('delete/{resource?}', [FolderController::class, 'delete'])->name('delete');
    });
});
