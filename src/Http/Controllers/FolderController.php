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
        if (!$request->canCreateFolder()) {
            throw ValidationException::withMessages([
                'folder' => [__('Sorry! You are not authorized to perform this action.')],
            ]);
        }

        $result = $request->manager()->mkdir(
            $path = trim($request->path)
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
        if (!$request->canRenameFolder()) {
            throw ValidationException::withMessages([
                'folder' => [__('Sorry! You are not authorized to perform this action.')],
            ]);
        }

        $oldPath = $request->oldPath;
        $newPath = $request->newPath;

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
        if (!$request->canDeleteFolder()) {
            throw ValidationException::withMessages([
                'folder' => [__('Sorry! You are not authorized to perform this action.')],
            ]);
        }

        $path = $request->path;

        $result = $request->manager()->rmdir($path);

        if (!$result) {
            throw ValidationException::withMessages([
                'folder' => [__('Could not delete folder !')],
            ]);
        }

        event(new FolderDeleted($request->manager()->disk, $path));

        return response()->json([
            'message' => __('Folder deleted successfully.'),
        ]);
    }
}
