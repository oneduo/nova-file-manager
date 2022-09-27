<?php

declare(strict_types=1);

namespace Oneduo\NovaFileManager\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Validation\ValidationException;
use Oneduo\NovaFileManager\Contracts\Filesystem\Upload\Uploader;
use Oneduo\NovaFileManager\Events\FileDeleted;
use Oneduo\NovaFileManager\Events\FileRenamed;
use Oneduo\NovaFileManager\Events\FileUnzipped;
use Oneduo\NovaFileManager\Http\Requests\DeleteFileRequest;
use Oneduo\NovaFileManager\Http\Requests\RenameFileRequest;
use Oneduo\NovaFileManager\Http\Requests\UnzipFileRequest;
use Oneduo\NovaFileManager\Http\Requests\UploadFileRequest;

class FileController extends Controller
{
    /**
     * Upload a file from the tool
     *
     * @param  \Oneduo\NovaFileManager\Http\Requests\UploadFileRequest  $request
     * @param  \Oneduo\NovaFileManager\Contracts\Filesystem\Upload\Uploader  $uploader
     * @return \Illuminate\Http\JsonResponse
     */
    public function upload(UploadFileRequest $request, Uploader $uploader): JsonResponse
    {
        return response()->json(
            $uploader->handle($request)
        );
    }

    /**
     * Rename a file
     *
     * @param  \Oneduo\NovaFileManager\Http\Requests\RenameFileRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function rename(RenameFileRequest $request): JsonResponse
    {
        $manager = $request->manager();

        $result = $manager->rename($request->from, $request->to);

        if (!$result) {
            throw ValidationException::withMessages([
                'from' => [__('nova-file-manager::errors.file.rename')],
            ]);
        }

        event(new FileRenamed($manager->disk, $request->from, $request->to));

        return response()->json([
            'message' => __('nova-file-manager::messages.file.rename'),
        ]);
    }

    /**
     * Delete a file
     *
     * @param  \Oneduo\NovaFileManager\Http\Requests\DeleteFileRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(DeleteFileRequest $request): JsonResponse
    {
        $manager = $request->manager();

        $result = $manager->delete($request->path);

        if (!$result) {
            throw ValidationException::withMessages([
                'path' => [__('nova-file-manager::errors.file.delete')],
            ]);
        }

        event(new FileDeleted($manager->disk, $request->path));

        return response()->json([
            'message' => __('nova-file-manager::messages.file.delete'),
        ]);
    }

    /**
     * Unzip an archive
     *
     * @param  \Oneduo\NovaFileManager\Http\Requests\UnzipFileRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function unzip(UnzipFileRequest $request): JsonResponse
    {
        $manager = $request->manager();

        $result = $manager->unzip($request->path);

        if (!$result) {
            throw ValidationException::withMessages([
                'path' => [__('nova-file-manager::errors.file.unzip')],
            ]);
        }

        event(new FileUnzipped($manager->disk, $request->path));

        return response()->json([
            'message' => __('nova-file-manager::messages.file.unzip'),
        ]);
    }
}
