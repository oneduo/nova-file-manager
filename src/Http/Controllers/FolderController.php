<?php

declare(strict_types=1);

namespace Oneduo\NovaFileManager\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Oneduo\NovaFileManager\Events\FolderCreated;
use Oneduo\NovaFileManager\Events\FolderCreating;
use Oneduo\NovaFileManager\Events\FolderDeleted;
use Oneduo\NovaFileManager\Events\FolderDeleting;
use Oneduo\NovaFileManager\Events\FolderRenamed;
use Oneduo\NovaFileManager\Events\FolderRenaming;
use Oneduo\NovaFileManager\Http\Requests\CreateFolderRequest;
use Oneduo\NovaFileManager\Http\Requests\DeleteFolderRequest;
use Oneduo\NovaFileManager\Http\Requests\RenameFolderRequest;

class FolderController extends Controller
{
    /**
     * Create a new folder
     */
    public function create(CreateFolderRequest $request): JsonResponse
    {
        $path = trim($request->path);

        $disk = config('filesystems.default');
        if (is_null($disk) || !strlen($disk)) {
            $disk = 'file_manager';
        }
        $filesystem = Storage::disk($disk);

        event(new FolderCreating($filesystem, $disk, $path));

        try {
            $result = $filesystem->makeDirectory($path);
        } catch (\Exception $e) {
            throw ValidationException::withMessages([
                'folder' => [__('nova-file-manager::errors.folder.create') . ' ' . $e->getMessage()],
            ]);
        }

        if (!$result) {
            throw ValidationException::withMessages([
                'folder' => [__('nova-file-manager::errors.folder.create')],
            ]);
        }

        event(new FolderCreated($filesystem, $disk, $path));

        return response()->json([
            'message' => __('nova-file-manager::messages.folder.create'),
        ]);
    }

    /**
     * Rename a folder
     */
    public function rename(RenameFolderRequest $request): JsonResponse
    {
        $from = $request->from;
        $to = $request->to;

        event(new FolderRenaming($request->manager()->filesystem(), $request->manager()->getDisk(), $from, $to));

        $result = $request->manager()->rename($from, $to);

        if (!$result) {
            throw ValidationException::withMessages([
                'folder' => [__('nova-file-manager::errors.folder.rename')],
            ]);
        }

        event(new FolderRenamed($request->manager()->filesystem(), $request->manager()->getDisk(), $from, $to));

        return response()->json([
            'message' => __('nova-file-manager::messages.folder.rename'),
        ]);
    }

    /**
     * Delete a folder
     */
    public function delete(DeleteFolderRequest $request): JsonResponse
    {
        $path = $request->path;

        event(new FolderDeleting($request->manager()->filesystem(), $request->manager()->getDisk(), $path));

        $result = $request->manager()->rmdir($path);

        if (!$result) {
            throw ValidationException::withMessages([
                'folder' => [__('nova-file-manager::errors.folder.delete')],
            ]);
        }

        event(new FolderDeleted($request->manager()->filesystem(), $request->manager()->getDisk(), $path));

        return response()->json([
            'message' => __('nova-file-manager::messages.folder.delete'),
        ]);
    }
}
