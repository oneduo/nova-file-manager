<?php

declare(strict_types=1);

namespace BBSLab\NovaFileManager\Services;

use BBSLab\NovaFileManager\Contracts\Services\FileManagerContract;
use BBSLab\NovaFileManager\Entities\Entity;
use Closure;
use Illuminate\Container\Container;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use League\Flysystem\UnableToRetrieveMetadata;

class FileManagerService implements FileManagerContract
{
    public FileSystem $fileSystem;

    public bool $shouldShowHiddenFiles;

    public array $filterCallbacks = [];

    public function __construct(
        public ?string $disk = null,
        public ?string $path = DIRECTORY_SEPARATOR,
        public int $page = 1,
        public int $perPage = 15,
        public ?string $search = null,
    ) {
        $this->disk ??= config('nova-file-manager.default_disk');

        $this->shouldShowHiddenFiles = config('nova-file-manager.show_hidden_files');

        $this->fileSystem = Storage::disk($this->disk);
    }

    /**
     * Set the current disk used by the service
     *
     * @param  string  $disk
     * @return $this
     */
    public function disk(string $disk): self
    {
        $this->disk = $disk;

        // create a new Filesystem instance based on the given disk
        $this->fileSystem = Storage::disk($this->disk);

        return $this;
    }

    /**
     * Set the current path used by the service
     *
     * @param  string  $path
     * @return $this
     */
    public function path(string $path): self
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Toggle the visibility of hidden files
     *
     * @param  bool  $show
     * @return $this
     */
    public function showHiddenFiles(bool $show = true): self
    {
        $this->shouldShowHiddenFiles = $show;

        return $this;
    }

    /**
     * Retrieve all the files in the current path
     *
     * @return \Illuminate\Support\Collection
     */
    public function files(): Collection
    {
        // register by default a call to omit hidden files
        $this->omitHiddenFilesAndDirectories();

        // register the search callback
        $this->applySearchCallback();

        return collect($this->fileSystem->files($this->path))
            ->filter(fn(string $file) => $this->applyFilterCallbacks($file));
    }

    /**
     * Retrieve all the directories in the current path
     *
     * @return \Illuminate\Support\Collection<array{id:string,path:string,name:string}
     */
    public function directories(): Collection
    {
        $this->omitHiddenFilesAndDirectories();

        return collect($this->fileSystem->directories($this->path))
            ->filter(fn(string $folder) => $this->applyFilterCallbacks($folder))
            // we map the folder to an array with an id, path and name
            ->map(fn(string $path) => [
                'id' => sha1($path),
                'path' => str($path)->start(DIRECTORY_SEPARATOR),
                'name' => pathinfo($path, PATHINFO_BASENAME),
            ])
            ->sortBy('path')
            ->values();
    }

    /**
     * Callback used to filter out hidden files if enabled
     *
     * @return void
     */
    public function omitHiddenFilesAndDirectories(): void
    {
        $this->filterCallbacks[] = $this->shouldShowHiddenFiles
            ? static fn() => true
            : static fn(string $path) => !str($path)->startsWith('.');
    }

    /**
     * Callback used to filter out files and directories based on a search query string
     *
     * @return void
     */
    public function applySearchCallback(): void
    {
        if (blank($this->search)) {
            return;
        }

        $this->filterCallbacks[] = function (string $path) {
            // split search string into words
            if (!$words = preg_split('/[\s]+/', $this->search)) {
                return true;
            }

            // join words with .* expression
            $words = implode('.*', array_map(fn(string $word) => preg_quote($word, '/'), $words));

            return preg_match("/(.*{$words}.*)/i", $path);
        };
    }

    /**
     * Callback used to apply all the registered filters
     *
     * @param  string  $value
     * @return bool
     */
    public function applyFilterCallbacks(string $value): bool
    {
        foreach ($this->filterCallbacks as $callback) {
            // if we fail the callback, we exit
            if (!$callback($value)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Build the current path's breadcrumbs
     *
     * @return \Illuminate\Support\Collection
     */
    public function breadcrumbs(): Collection
    {
        $paths = collect();

        str($this->path)
            ->ltrim(DIRECTORY_SEPARATOR)
            ->explode(DIRECTORY_SEPARATOR)
            ->filter(fn(string $item) => !blank($item))
            ->each(function (string $item) use ($paths) {
                return $paths->push($paths->last().DIRECTORY_SEPARATOR.$item);
            });

        // we map the folders to match the breadcrumbs format
        return $paths->map(fn(string $item) => [
            'id' => sha1($item),
            'path' => $item,
            'name' => str($item)->afterLast('/'),
            'current' => $item === $this->path,
        ]);
    }

    /**
     * Create a new directory in the current path
     *
     * @param  string  $path
     * @return bool
     */
    public function mkdir(string $path): bool
    {
        if (!$this->fileSystem->exists($path)) {
            return $this->fileSystem->makeDirectory($path);
        }

        return false;
    }

    /**
     * Remove a directory from the disk
     *
     * @param  string  $path
     * @return bool
     */
    public function rmdir(string $path): bool
    {
        return $this->fileSystem->deleteDirectory($path);
    }

    /**
     * Rename a directory or file in the disk
     *
     * @param  string  $oldPath
     * @param  string  $newPath
     * @return bool
     */
    public function rename(string $oldPath, string $newPath): bool
    {
        return $this->fileSystem->move($oldPath, $newPath);
    }

    /**
     * Remove a file from the disk
     *
     * @param  string  $path
     * @return bool
     */
    public function delete(string $path): bool
    {
        return $this->fileSystem->delete($path);
    }

    /**
     * Paginate the directories and files in the current path from the current disk
     *
     * @param  \Illuminate\Support\Collection  $data
     * @return \Illuminate\Pagination\LengthAwarePaginator
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function paginate(Collection $data): LengthAwarePaginator
    {
        return Container::getInstance()
            ->makeWith(LengthAwarePaginator::class, [
                'items' => $data
                    ->forPage($this->page, $this->perPage)
                    ->values()
                    ->map($this->mapIntoEntity())
                    ->toArray(),
                'total' => $data->count(),
                'perPage' => $this->perPage,
                'currentPage' => Paginator::resolveCurrentPage(),
            ]);
    }

    /**
     * Set the pagination parameters
     *
     * @param  int  $page
     * @param  int  $perPage
     * @return $this
     */
    public function forPage(int $page, int $perPage): self
    {
        $this->page = $page;

        $this->perPage = $perPage;

        return $this;
    }

    /**
     * Map the data into a class-based entity
     *
     * @return \Closure
     */
    public function mapIntoEntity(): Closure
    {
        return fn(string $path) => $this->makeEntity($path);
    }

    /**
     * Create a new entity from the given path
     *
     * @param  string  $path
     * @return \BBSLab\NovaFileManager\Entities\Entity
     */
    public function makeEntity(string $path): Entity
    {
        try {
            $mime = $this->fileSystem->mimeType($path);
            $type = $mime
                ? Str::before($mime, DIRECTORY_SEPARATOR)
                : 'default';
        } catch (UnableToRetrieveMetadata $e) {
            report($e);

            $type = 'default';
        }

        return $this->entityClassForType($type)::make($this->disk, $path);
    }

    /**
     * Get the corresponding class for the given file type
     *
     * @param  string  $type
     * @return class-string
     */
    public function entityClassForType(string $type): string
    {
        return config('nova-file-manager.entities.'.$type) ?? config('nova-file-manager.entities.default');
    }

    /**
     * Static helper
     *
     * @param  string|null  $disk
     * @param  string|null  $path
     * @param  int  $page
     * @param  int  $perPage
     * @param  string|null  $search
     * @return static
     */
    public static function make(
        ?string $disk = null,
        ?string $path = null,
        int $page = 1,
        int $perPage = 15,
        ?string $search = null
    ): static {
        return new self($disk, $path, $page, $perPage, $search);
    }
}
