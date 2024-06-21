<?php

declare(strict_types=1);

namespace Oneduo\NovaFileManager\Services;

use Closure;
use Illuminate\Container\Container;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use League\Flysystem\UnableToRetrieveMetadata;
use Oneduo\NovaFileManager\Contracts\Services\FileManagerContract;
use Oneduo\NovaFileManager\Contracts\Support\ResolvesUrl as ResolvesUrlContract;
use Oneduo\NovaFileManager\Entities\Entity;
use Oneduo\NovaFileManager\Traits\Support\ResolvesUrl;

class FileManagerService implements FileManagerContract, ResolvesUrlContract
{
    use ResolvesUrl;

    public string $disk;

    public FileSystem $filesystem;

    public bool $shouldShowHiddenFiles;

    public array $filterCallbacks = [];

    public function __construct(
        string|Filesystem|null $disk = null,
        public ?string $path = DIRECTORY_SEPARATOR,
        public int $page = 1,
        public int $perPage = 15,
        public ?string $search = null,
    ) {
        $this->setDisk($disk ?? config('nova-file-manager.default_disk'));

        $this->shouldShowHiddenFiles = config('nova-file-manager.show_hidden_files');
    }

    /**
     * Set the current disk used by the service
     *
     * @return $this
     */
    public function setDisk(string|Filesystem $disk): self
    {
        if ($disk instanceof Filesystem) {
            $this->disk = 'default';

            $this->filesystem = $disk;
        } else {
            $this->disk = $disk;

            // create a new Filesystem instance based on the given disk
            $this->filesystem = Storage::disk($this->disk);
        }

        return $this;
    }

    public function getDisk(): string
    {
        return $this->disk;
    }

    /**
     * Set the current path used by the service
     *
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
     * @return $this
     */
    public function showHiddenFiles(bool $show = true): self
    {
        $this->shouldShowHiddenFiles = $show;

        return $this;
    }

    /**
     * Get the current filesystem instance
     */
    public function filesystem(): Filesystem
    {
        return $this->filesystem;
    }

    /**
     * Retrieve all the files in the current path
     */
    public function files(): Collection
    {
        // register by default a call to omit hidden files
        $this->omitHiddenFilesAndDirectories();

        // register the search callback
        $this->applySearchCallback();

        return collect($this->filesystem->files($this->path))
            ->filter(fn (string $file) => $this->applyFilterCallbacks($file));
    }

    /**
     * Retrieve all the directories in the current path
     *
     * @return \Illuminate\Support\Collection<array{id:string,path:string,name:string}
     */
    public function directories(): Collection
    {
        $this->omitHiddenFilesAndDirectories();

        return collect($this->filesystem->directories($this->path))
            ->filter(fn (string $folder) => $this->applyFilterCallbacks($folder))
            // we map the folder to an array with an id, path and name
            ->map(fn (string $path) => [
                'id' => sha1($path),
                'path' => str($path)->start(DIRECTORY_SEPARATOR),
                'name' => pathinfo($path, PATHINFO_BASENAME),
                'type' => 'folder',
            ])
            ->sortBy('path')
            ->values();
    }

    /**
     * Callback used to filter out hidden files if enabled
     */
    public function omitHiddenFilesAndDirectories(): void
    {
        $this->filterCallbacks[] = $this->shouldShowHiddenFiles
            ? static fn () => true
            : static fn (string $path) => !str($path)->afterLast('/')->startsWith('.');
    }

    /**
     * Callback used to filter out files and directories based on a search query string
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
            $words = implode('.*', array_map(fn (string $word) => preg_quote($word, '/'), $words));

            return preg_match("/(.*{$words}.*)/i", $path);
        };
    }

    /**
     * Callback used to apply all the registered filters
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
     */
    public function breadcrumbs(): Collection
    {
        $paths = collect();

        str($this->path)
            ->ltrim(DIRECTORY_SEPARATOR)
            ->explode(DIRECTORY_SEPARATOR)
            ->filter(fn (string $item) => !blank($item))
            ->each(function (string $item) use ($paths) {
                return $paths->push($paths->last().DIRECTORY_SEPARATOR.$item);
            });

        // we map the folders to match the breadcrumbs format
        return $paths->map(fn (string $item) => [
            'id' => sha1($item),
            'path' => $item,
            'name' => str($item)->afterLast('/'),
            'current' => $item === $this->path,
        ]);
    }

    /**
     * Create a new directory in the current path
     */
    public function mkdir(string $path): bool
    {
        if (!$this->filesystem->exists($path)) {
            return $this->filesystem->makeDirectory($path);
        }

        return false;
    }

    /**
     * Remove a directory from the disk
     */
    public function rmdir(string $path): bool
    {
        return $this->filesystem->deleteDirectory($path);
    }

    /**
     * Rename a directory or file in the disk
     */
    public function rename(string $from, string $to): bool
    {
        return $this->filesystem->move($from, $to);
    }

    /**
     * Remove a file from the disk
     */
    public function delete(string $path): bool
    {
        return $this->filesystem->delete($path);
    }

    /**
     * Unzip an archive to the current path
     *
     * Note: ext-zip is required
     */
    public function unzip(string $path): bool
    {
        // we check the mime type to ensure it is a zip file
        if ($this->filesystem->mimeType($path) !== 'application/zip') {
            return false;
        }

        // if ext-zip is not available, we do nothing
        if (!class_exists(\ZipArchive::class)) {
            return false;
        }

        $zip = new \ZipArchive();

        // open the zip archive
        $zip->open($this->filesystem->path($path));

        // create the target folder
        $destination = (string) str($zip->filename)->basename()->replace('.zip', '');

        if (!$this->mkdir($destination)) {
            return false;
        }

        // extract the contents
        $zip->extractTo($this->filesystem->path($destination));

        return $zip->close();
    }

    /**
     * Paginate the directories and files in the current path from the current disk
     *
     *
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
     */
    public function mapIntoEntity(): Closure
    {
        return fn (string $path) => $this->makeEntity($path, $this->getDisk());
    }

    /**
     * Create a new entity from the given path
     */
    public function makeEntity(string $path, string $disk): Entity
    {
        try {
            $mime = $this->filesystem->mimeType($path);
            $type = $mime ? Str::before($mime, DIRECTORY_SEPARATOR) : 'default';
        } catch (UnableToRetrieveMetadata $e) {
            report($e);

            $type = 'default';
        }

        return $this->entityClassForType($type)::make($this, $path, $disk);
    }

    /**
     * Get the corresponding class for the given file type
     *
     * @return class-string
     */
    public function entityClassForType(string $type): string
    {
        return config('nova-file-manager.entities.'.$type) ?? config('nova-file-manager.entities.default');
    }

    /**
     * Static helper
     */
    public static function make(
        string|Filesystem|null $disk = null,
        ?string $path = null,
        int $page = 1,
        int $perPage = 15,
        ?string $search = null
    ): static {
        return new self($disk, $path, $page, $perPage, $search);
    }
}
