<?php

declare(strict_types=1);

namespace BBSLab\NovaFileManager\Contracts\Services;

use BBSLab\NovaFileManager\Entities\Entity;
use Closure;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface FileManagerContract
{
    public function applyFilterCallbacks(string $value): bool;

    public function applySearchCallback(): void;

    public function breadcrumbs(): Collection;

    public function delete(string $path): bool;

    public function directories(): Collection;

    public function disk(string $disk): self;

    public function entityClassForType(string $type): string;

    public function files(): Collection;

    public function forPage(int $page, int $perPage): self;

    public function makeEntity(string $path): Entity;

    public function mapIntoEntity(): Closure;

    public function mkdir(string $path): bool;

    public function omitHiddenFilesAndDirectories(): void;

    public function paginate(Collection $data): LengthAwarePaginator;

    public function path(string $path): self;

    public function rename(string $oldPath, string $newPath): bool;

    public function rmdir(string $path): bool;

    public function showHiddenFiles(bool $show = true): self;

    public static function make(
        ?string $disk = null,
        ?string $path = null,
        int $page = 1,
        int $perPage = 15,
        ?string $search = null
    ): self;
}
