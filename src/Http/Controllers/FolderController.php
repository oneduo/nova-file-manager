<?php

declare(strict_types=1);

namespace BBSLab\NovaFileManager\Http\Controllers;

use BBSLab\NovaFileManager\Events\FolderCreated;
use BBSLab\NovaFileManager\Events\FolderDeleted;
use BBSLab\NovaFileManager\Events\FolderRenamed;
use BBSLab\NovaFileManager\Http\Requests\CreateFolderRequest;
use BBSLab\NovaFileManager\Http\Requests\DeleteFolderRequest;
use BBSLab\NovaFileManager\Http\Requests\RenameFolderRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Validation\ValidationException;

class FolderController extends Controller
{
    /**
     * Create a new folder
     *
     * @param  \BBSLab\NovaFileManager\Http\Requests\CreateFolderRequest  $request
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
     * @param  \BBSLab\NovaFileManager\Http\Requests\RenameFolderRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function rename(RenameFolderRequest $request): JsonResponse
    {
        $oldPath = $request->oldPath;
        $newPath = $request->newPath;

        $result = $request->manager()->rename($oldPath, $newPath);

        if (!$result) {
            throw ValidationException::withMessages([
                'folder' => [__('nova-file-manager::errors.folder.rename')],
            ]);
        }

        event(new FolderRenamed($request->manager()->disk, $oldPath, $newPath));

        return response()->json([
            'message' => __('nova-file-manager::messages.folder.rename'),
        ]);
    }

    /**
     * Delete a folder
     *
     * @param  \BBSLab\NovaFileManager\Http\Requests\DeleteFolderRequest  $request
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
