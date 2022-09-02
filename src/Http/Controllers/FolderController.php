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
            $path = trim($request->get('path'))
        );

        if (!$result) {
            throw ValidationException::withMessages([
                'folder' => [__('Folder already exists !')],
            ]);
        }

        event(new FolderCreated($request->manager()->disk, $path));

        return response()->json([
            'message' => __('Folder created successfully.'),
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
        $oldPath = $request->get('oldPath');
        $newPath = $request->get('newPath');

        $result = $request->manager()->rename($oldPath, $newPath);

        if (!$result) {
            throw ValidationException::withMessages([
                'folder' => [__('Could not rename folder !')],
            ]);
        }

        event(new FolderRenamed($request->manager()->disk, $oldPath, $newPath));

        return response()->json([
            'message' => __('Folder renamed successfully.'),
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
        $path = $request->get('path');
        $disk = $request->get('disk');

        $result = $request->manager()->rmdir($request->path);

        if (!$result) {
            throw ValidationException::withMessages([
                'folder' => [__('Could not delete folder !')],
            ]);
        }

        event(new FolderDeleted($disk, $path));

        return response()->json([
            'message' => __('Folder deleted successfully.'),
        ]);
    }
}
