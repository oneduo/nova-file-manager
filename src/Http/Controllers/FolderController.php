<?php

declare(strict_types=1);

namespace Oneduo\NovaFileManager\Http\Controllers;

use Oneduo\NovaFileManager\Events\FolderCreated;
use Oneduo\NovaFileManager\Events\FolderDeleted;
use Oneduo\NovaFileManager\Events\FolderRenamed;
use Oneduo\NovaFileManager\Http\Requests\CreateFolderRequest;
use Oneduo\NovaFileManager\Http\Requests\DeleteFolderRequest;
use Oneduo\NovaFileManager\Http\Requests\RenameFolderRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Validation\ValidationException;

class FolderController extends Controller
{
    /**
     * Create a new folder
     *
     * @param  \Oneduo\NovaFileManager\Http\Requests\CreateFolderRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(CreateFolderRequest $request): JsonResponse
    {
        $result = $request->manager()->mkdir(
            $path = trim($request->path)
        );

        if (!$result) {
            throw ValidationException::withMessages([
                'folder' => [__('nova-file-manager::errors.folder.create')],
            ]);
        }

        event(new FolderCreated($request->manager()->disk, $path));

        return response()->json([
            'message' => __('nova-file-manager::messages.folder.create'),
        ]);
    }

    /**
     * Rename a folder
     *
     * @param  \Oneduo\NovaFileManager\Http\Requests\RenameFolderRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function rename(RenameFolderRequest $request): JsonResponse
    {
        $from = $request->from;
        $to = $request->to;

        $result = $request->manager()->rename($from, $to);

        if (!$result) {
            throw ValidationException::withMessages([
                'folder' => [__('nova-file-manager::errors.folder.rename')],
            ]);
        }

        event(new FolderRenamed($request->manager()->disk, $from, $to));

        return response()->json([
            'message' => __('nova-file-manager::messages.folder.rename'),
        ]);
    }

    /**
     * Delete a folder
     *
     * @param  \Oneduo\NovaFileManager\Http\Requests\DeleteFolderRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(DeleteFolderRequest $request): JsonResponse
    {
        $path = $request->path;

        $result = $request->manager()->rmdir($path);

        if (!$result) {
            throw ValidationException::withMessages([
                'folder' => [__('nova-file-manager::errors.folder.delete')],
            ]);
        }

        event(new FolderDeleted($request->manager()->disk, $path));

        return response()->json([
            'message' => __('nova-file-manager::messages.folder.delete'),
        ]);
    }
}
