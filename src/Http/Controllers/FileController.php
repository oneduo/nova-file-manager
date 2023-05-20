<?php

declare(strict_types=1);

namespace Oneduo\NovaFileManager\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Validation\ValidationException;
use Oneduo\NovaFileManager\Contracts\Filesystem\Upload\Uploader;
use Oneduo\NovaFileManager\Events\FileDeleted;
use Oneduo\NovaFileManager\Events\FileDeleting;
use Oneduo\NovaFileManager\Events\FileRenamed;
use Oneduo\NovaFileManager\Events\FileRenaming;
use Oneduo\NovaFileManager\Events\FileUnzipped;
use Oneduo\NovaFileManager\Events\FileUnzipping;
use Oneduo\NovaFileManager\Http\Requests\DeleteFileRequest;
use Oneduo\NovaFileManager\Http\Requests\RenameFileRequest;
use Oneduo\NovaFileManager\Http\Requests\UnzipFileRequest;
use Oneduo\NovaFileManager\Http\Requests\UploadFileRequest;

class FileController extends Controller
{
    /**
     * Upload a file from the tool
     */
    public function upload(UploadFileRequest $request, Uploader $uploader): JsonResponse
    {
        return response()->json(
            $uploader->handle($request)
        );
    }

    /**
     * Rename a file
     */
    public function rename(RenameFileRequest $request): JsonResponse
    {
        $manager = $request->manager();

        event(new FileRenaming($manager->filesystem(), $manager->getDisk(), $request->from, $request->to));

        $result = $manager->rename($request->from, $request->to);

        if (! $result) {
            throw ValidationException::withMessages([
                'from' => [__('nova-file-manager::errors.file.rename')],
            ]);
        }

        event(new FileRenamed($manager->filesystem(), $manager->getDisk(), $request->from, $request->to));

        return response()->json([
            'message' => __('nova-file-manager::messages.file.rename'),
        ]);
    }

    /**
     * Delete a file
     */
    public function delete(DeleteFileRequest $request): JsonResponse
    {
        $manager = $request->manager();

        foreach ($request->paths as $path) {
            event(new FileDeleting($manager->filesystem(), $manager->getDisk(), $path));

            $result = $manager->delete($path);

            if (! $result) {
                throw ValidationException::withMessages([
                    'paths' => [__('nova-file-manager::errors.file.delete')],
                ]);
            }

            event(new FileDeleted($manager->filesystem(), $manager->getDisk(), $path));
        }

        return response()->json([
            'message' => __('nova-file-manager::messages.file.delete'),
        ]);
    }

    /**
     * Unzip an archive
     */
    public function unzip(UnzipFileRequest $request): JsonResponse
    {
        $manager = $request->manager();

        event(new FileUnzipping($manager->filesystem(), $manager->getDisk(), $request->path));

        $result = $manager->unzip($request->path);

        if (! $result) {
            throw ValidationException::withMessages([
                'path' => [__('nova-file-manager::errors.file.unzip')],
            ]);
        }

        event(new FileUnzipped($manager->filesystem(), $manager->getDisk(), $request->path));

        return response()->json([
            'message' => __('nova-file-manager::messages.file.unzip'),
        ]);
    }
}
