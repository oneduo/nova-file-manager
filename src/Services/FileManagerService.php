<?php

declare(strict_types=1);

namespace BBSLab\NovaFileManager\Services;

use BBSLab\NovaFileManager\Contracts\FileManagerContract;
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

    public function disk(string $disk): self
    {
        $this->disk = $disk;

        $this->fileSystem = Storage::disk($this->disk);

        return $this;
    }

    public function path(string $path): self
    {
        $this->path = $path;

        return $this;
    }

    public function showHiddenFiles(bool $show = true): self
    {
        $this->shouldShowHiddenFiles = $show;

        return $this;
    }

    public function files(): Collection
    {
        $this->omitHiddenFilesAndDirectories();
        $this->applySearchCallback();

        return collect($this->fileSystem->files($this->path))
            ->filter(fn (string $file) => $this->applyFilterCallbacks($file));
    }

    public function omitHiddenFilesAndDirectories(): void
    {
        $this->filterCallbacks[] = $this->shouldShowHiddenFiles
            ? static fn () => true
            : static fn (string $path) => !str($path)->startsWith('.');
    }

    public function applySearchCallback(): void
    {
        if (empty($this->search)) {
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

    public function applyFilterCallbacks(string $value): bool
    {
        foreach ($this->filterCallbacks as $callback) {
            if (!$callback($value)) {
                return false;
            }
        }

        return true;
    }

    public function directories(): Collection
    {
        $this->omitHiddenFilesAndDirectories();

        return collect($this->fileSystem->directories($this->path))
            ->filter(fn (string $file) => $this->applyFilterCallbacks($file))
            ->map(fn (string $path) => [
                'id' => Str::random(6),
                'path' => str($path)->start(DIRECTORY_SEPARATOR),
                'name' => pathinfo($path, PATHINFO_BASENAME),
            ])
            ->sortBy('path')
            ->values();
    }

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

        return $paths->map(fn (string $item) => [
            'id' => Str::random(6),
            'path' => $item,
            'name' => str($item)->afterLast('/'),
            'current' => $item === $this->path,
        ]);
    }

    public function mkdir(string $path): bool
    {
        if (!$this->fileSystem->exists($path)) {
            return $this->fileSystem->makeDirectory($path);
        }

        return false;
    }

    public function rmdir(string $path): bool
    {
        return $this->fileSystem->deleteDirectory($path);
    }

    public function rename(string $oldPath, string $newPath): bool
    {
        return $this->fileSystem->move($oldPath, $newPath);
    }

    public function delete(string $path): bool
    {
        return $this->fileSystem->delete($path);
    }

    /**
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

    public function forPage(int $page, int $perPage): self
    {
        $this->page = $page;

        $this->perPage = $perPage;

        return $this;
    }

    public function mapIntoEntity(): Closure
    {
        return fn (string $path) => $this->makeEntity($path);
    }

    public function makeEntity(string $path)
    {
        try {
            $mime = $this->fileSystem->mimeType($path);
            $type = str($mime)->before('/')->toString();
        } catch (UnableToRetrieveMetadata $e) {
            report($e);

            $type = 'default';
        }

        return $this->entityClassForType($type)::make($this->disk, $path);
    }

    public static function make(
        ?string $disk = null,
        ?string $path = null,
        int $page = 1,
        int $perPage = 15,
        ?string $search = null
    ): self {
        return new self($disk, $path, $page, $perPage, $search);
    }

    /**
     * @param  string  $type
     * @return <class-string>
     */
    public function entityClassForType(string $type): string
    {
        return config('nova-file-manager.entities.'.$type) ?? config('nova-file-manager.entities.default');
    }
}
