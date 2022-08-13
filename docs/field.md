# Using the field

## Using the field on a Nova Resource

You can start using the field by adding a `FileManager` field to your Nova resource :

```php
// app/Nova/Project.php

use BBSLab\NovaFileManager\FileManager;

class Project extends Resource
{
    // ...

    public function fields(NovaRequest $request): array
    {
        return [
            // ... any other fields
            FileManager::make(__('Attachments'), 'attachments'),
        ];
    }
}
```

🎉 You have now successfully added a File Manager field to your resource.

<img src="./images/field.png" alt="field"/>

## Multiple selection on the form field

When using the `FileManager` field on your Nova resource, you can tell the tool to allow multiple selection for your attribute.

By default, the tool will only allow single selection.

You can allow multiple selection by using the `multiple` method to the field. You may limit the number of selected field by using the `limit` method.

```php
// app/Nova/Project.php

use BBSLab\NovaFileManager\FileManager;

class Project extends Resource
{
    // ...

    public function fields(NovaRequest $request): array
    {
        return [
            // ... any other fields
            FileManager::make(__('Attachments'), 'attachments')
                ->multiple()
                ->limit(10),
        ];
    }
}
```

> **Note** If the limit is set to 1, the field saves the value as a plain string containing the file's path in the specified storage disk. For any value greater than 1, the field saves the value as an array of file paths. You can access these paths easily by setting a cast on your attribute.

```php
// app/Models/Project.php

class Project extends Authenticatable
{
    // ...

    protected $fillable = [
        'name',
        'email',
        'password',
        'attachments',
    ];

    protected $casts = [
        'attachments' => 'array',
    ];
}

```

## Validation

When using the field, you can specify the number of files that can be set a value for your resource's attribute.

For that, you can specifically use the following custom rule :

```php
// app/Nova/Project.php

use BBSLab\NovaFileManager\FileManager;
use BBSLab\NovaFileManager\Rules\FileLimit;

class Project extends Resource
{
    // ...

    public function fields(NovaRequest $request): array
    {
        return [
            // ... any other fields
            FileManager::make(__('Attachments'), 'attachments')
                ->rules(new FileLimit(min: 3, max: 10))
                ->multiple()
                ->limit(10),
        ];
    }
}
```

> **Note** You need to set up your field with `multiple` if you plan on having a minimum value greater than one, and if you expect your field to have more than one file.

## Saving the disk name alongside the path

When using a multi-disk setup, you may want to save the disk from which the file has been picked and save it into your
resource, so that, for instance, you can generate a valid url depending on the disk.

To save the disk, you'll need to add a new column to your model, to store the value.

For instance, having a `User` model to which we want to add an avatar, you can run a migration like the following :

```php
public function up(): void
{
    Schema::table('users', function (Blueprint $table) {
        $table->string('avatar')->nullable();
        $table->string('avatar_disk')->nullable();
    });
}
```

And then set up the field :

```php
FileManager::make(__('Avatar'), 'avatar')->storeDisk('avatar_disk')
```