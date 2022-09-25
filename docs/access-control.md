# Access control

The package provides some convenient methods to control how the users access the different features provided by the file
manager, such as creating a folder, upload a file or deleting it. With granular control in mind, you can use these
methods to build your access control flow with freedom and flexibility.
 
## Showing or hiding buttons

Say for instance your app relies on a role/permission based system, in which you may want to grant the ability of
creating folder to only a subset of your users. For this, you can use specific methods on your field to show the
different action buttons avaiable in the browser modal.

Available methods :

- `showCreateFolder`
- `showRenameFolder`
- `showDeleteFolder`
- `showUploadFile`
- `showRenameFile`
- `showDeleteFile`
- `showCropImage`

The usage is pretty straightforward, you can pass a callback that takes the current `NovaRequest` as a parameter, that
resolves into `true|false`, like so :

```php
// app/Nova/Project.php

use Oneduo\NovaFileManager\FileManager;
use Oneduo\NovaFileManager\Http\Requests\CreateFolderRequest;
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
                ->showCreateFolder(function (NovaRequest $request): bool {
                    // in this instance we only allow the creation of folders to users that pass the `isAdmin()` check
                    return $request->user()?->isAdmin();
                }),
        ];
    }
}
```

::: info
Using one of these methods does impact the resolution of the API call associated with the desired action. In
other words, in the previous example, setting the visibility of the create folder button to only admin in our app, does
in fact prevent all API requests that do not match this condition. Preventing the creation of a folder by a non-admin
user.
:::

## Restricting access to specific actions

The file manager does provide methods to restrict the access to the different actions based on your own logic if needed.
Like the previous example, we can use the following method to implement granular access control :

Available methods :

- `canCreateFolder`
- `canRenameFolder`
- `canDeleteFolder`
- `canUploadFile`
- `canRenameFile`
- `canDeleteFile`

Unlike the `show` prefixed methods, these aformentionned methods do not hide the buttons from the tool, they only
restrict the underlying API actions.

To use these methods, you can pass a callback that takes the corresponding request as a parameter, for instance, upon
creating a folder, you can use the following callback :

```php
// app/Nova/Project.php

use Oneduo\NovaFileManager\FileManager;
use Oneduo\NovaFileManager\Http\Requests\CreateFolderRequest;
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
                ->canCreateFolder(function (CreateFolderRequest $request) {
                    // in this example we only allow the creation of folders for users that have the corresponding ability
                    return $request->user()?->can('create-folder');
                }),
        ];
    }
}
```

You can even go the extra mile and customize the callback to, for example, throw a `ValidationException` with a custom
error message that you can set yourself :

```php
// app/Nova/Project.php

use Oneduo\NovaFileManager\FileManager;
use Oneduo\NovaFileManager\Http\Requests\CreateFolderRequest;
use Illuminate\Support\Facades\Storage;
use Laravel\Nova\Http\Requests\NovaRequest;

class Project extends Resource
{
    // ...

    public function fields(NovaRequest $request): array
    {
        return [
            // ... any other fields
            NovaFileManager::make()
                ->canDeleteFolder(function (DeleteFolderRequest $request) {
                    // check if the current user has the ability to delete the selected folder using its path
                    if (!$request->user()?->can('delete-folder', ['folder' => $request->input('path')])) {
                    
                        throw ValidationException::withMessages([
                            // folder for directory operations, and file for file operations
                            // must return a string[]
                            'folder' => [
                                'You are not allowed to delete this folder',
                            ],
                        ]);
                        
                    }
    
                    return true;
                }),
        ];
    }
}
```

## On-demand filesystem

By default, the package shows the file manager using the configured disks. However, you may want to define a particular
disk or directory based on your own business logic.

For instance, having a multi role/permission based app, let's say you have 2 user groups, Managers and Employees. You
want to restrict each user group inside a closed user-land filesystem, in such a way that no user group can access the
other user group's files.

Typically, this can be done by having two filesystem disks defined in your `filesystems.php` and setting the root for
each.

```php
// config/filesystems.php

return [
    // ...  
    'disks' => [
        // ...
        'managers' => [
            'driver' => 'local',
            'root' => storage_path('app/public/managers'),
            'url' => env('APP_URL').'/storage/managers',
            'visibility' => 'public',
            'throw' => false,
        ],
    ],
];
```

```php
// app/Nova/Project.php

use Oneduo\NovaFileManager\FileManager;
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
                    return Storage::disk('managers');
                }),
        ];
    }
}
```

In addition, our tool grants you the ability to define the filesystem on the fly, based on your own logic.

To do so, you may use the `filesystem()` method, which takes a callback as a parameter, that takes the
current `NovaRequest` as a parameter, and returns a `\Illuminate\Contracts\Filesystem\Filesystem` instance.

In the example below, you build an [on-demand disk](https://laravel.com/docs/9.x/filesystem#on-demand-disks) that has a
root, a folder based on the current user's role :

```php
// app/Nova/Project.php

use Oneduo\NovaFileManager\FileManager;
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
                        'driver' => 'local', // you may use any of the Laravel's builtin filesystem drivers
                        'root' => storage_path('app/public/'.$request->user()->role()), // for a "manager" the root is "storage/app/public/manager"
                        'url' => config('app.url').'/storage/'.$request->user()->role(), // for a "manager" the url is built using http://localhost/storage/manager
                    ]);
                }),
        ];
    }
}
```

