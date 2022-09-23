<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Oneduo\NovaFileManager\Http\Controllers\ToolController;

/*
|--------------------------------------------------------------------------
| Tool Routes
|--------------------------------------------------------------------------
|
| Here is where you may register Inertia routes for your tool. These are
| loaded by the ServiceProvider of the tool. The routes are protected
| by your tool's "Authorize" middleware by default. Now - go build!
|
*/

Route::get('/', ToolController::class)->name('nova-file-manager.tool');
