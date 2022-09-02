# Permissions

The package provides some convenient methods to allow or not features like creating a folder or deleting a file. All following methods are available both on the tool and the field.

## Custom filesystem

By default, the package show the filemanager with all configured disks. Sometimes, you may want to define a particular disk or directory as the root of the filemanager.

```php
// app/Nova/Project.php

use BBSLab\NovaFileManager\FileManager;
use Illuminate\Support\Facades\Storage;
use Laravel\Nova\Http\Requests\NovaRequest;

class Project extends Resource
{
    // ...

    public function fields(NovaRequest $request): array
    {
        return [
            // ... any other fields
            FileManager::make(__('Attachments'), 'attachments')
                ->filesystem(function (NovaRequest $request) {
                    // create a filesystem on user basis
                    return Storage::build([
                        'driver' => 'local',
                        'root' => storage_path('app/public/'.$request->user()->getKey()),
                        'url' => env('APP_URL').'/storage/'.$request->user()->getKey(),
                        'visibility' => 'public',
                    ]);
                }),
        ];
    }
}
```

The closure accepts a `\Laravel\Nova\Http\Requests\NovaRequest $request` parameter and should return a `\Illuminate\Contracts\Filesystem\Filesystem` instance.

### Authorization

#### Create a folder

You may hide the create folder button :

```php
// app/Nova/Project.php

use BBSLab\NovaFileManager\FileManager;
use BBSLab\NovaFileManager\Http\Requests\CreateFolderRequest;
use Illuminate\Support\Facades\Storage;
use Laravel\Nova\Http\Requests\NovaRequest;

class Project extends Resource
{
    // ...

    public function fields(NovaRequest $request): array
    {
        return [
            // ... any other fields
            FileManager::make(__('Attachments'), 'attachments')
                ->showCreateFolder(function (NovaRequest $request) {
                    return $request->user()?->isAdmin();
                }),
        ];
    }
}
```

The closure is also used to prevent api calls. You may want to use another logic to allow or deny the api call, you may use in addition the `canCreateFolder` method :

```php
// app/Nova/Project.php

use BBSLab\NovaFileManager\FileManager;
use BBSLab\NovaFileManager\Http\Requests\CreateFolderRequest;
use Illuminate\Support\Facades\Storage;
use Laravel\Nova\Http\Requests\NovaRequest;

class Project extends Resource
{
    // ...

    public function fields(NovaRequest $request): array
    {
        return [
            // ... any other fields
            FileManager::make(__('Attachments'), 'attachments')
                ->showCreateFolder(function (NovaRequest $request) {
                    return $request->user()?->isAdmin();
                })
                ->canCreateFolder(function (CreateFolderRequest $request) {
                    return str_contains($request->path, 'foo');
                }),
        ];
    }
}
```