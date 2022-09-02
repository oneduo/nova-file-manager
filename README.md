<img src="./docs/banner.png"/>
<h3 align="left">Nova File Manager</h3>

<div align="left">

![Status](https://img.shields.io/badge/status-active-success.svg)
[![License](https://img.shields.io/badge/license-MIT-blue.svg)](/LICENSE)
![PHP](https://img.shields.io/badge/PHP-8-blue.svg)
![Laravel Nova](https://img.shields.io/badge/laravel%2Fnova-4-cyan.svg)
[![Latest Version on Packagist](https://img.shields.io/packagist/v/bbs-lab/nova-file-manager.svg)](https://packagist.org/packages/bbs-lab/nova-file-manager)
[![Total Downloads](https://img.shields.io/packagist/dt/bbs-lab/nova-file-manager.svg)](https://packagist.org/packages/bbs-lab/nova-file-manager)
[![Run tests](https://github.com/BBS-Lab/nova-file-manager/actions/workflows/tests.yml/badge.svg?branch=main)](https://github.com/BBS-Lab/nova-file-manager/actions/workflows/tests.yml)
</div>

---

<p>
A file manager tool and field for Laravel Nova. Beautifully designed, and customizable, this tool will provide a plug'n'play solution for your file management needs.

**Features :**

⚡️ Blazing fast  
📦️ Laravel Nova 4 compatible  
💅 Built with Tailwindcss 3, Vue 3 and Vuex  
💽 Multi disk and filesystem support  
🧩 Supports chunk uploads  
🔧 Various customization and configuration options  
🔍 A performant local search feature  
🤹‍ Allows to save multiple assets on the same field
</p>

**Documentation** is available here : https://bbs-lab.github.io/nova-file-manager/


<img src="./docs/preview.png"/>

<img src="./docs/subbanner.png"/>

## Table of Contents

- [Getting Started](#getting_started)
  - [Prerequisites](#prerequisites)
  - [Installing](#installing)
  - [Configuration](#configuration)
- [Usage](#usage)
  - [Advanced usage](#advanced-usage)
    - [Multiple selection on the form field](#multiple-selection-on-the-form-field)
    - [Validation](#validation)
    - [Saving the disk name alongside the path](#saving-the-disk-name-alongside-the-path)
- [Configuration](#configuration-file)
- [Authors](#authors)
- [Screenshots](#screenshots)
- [Changelog](#changelog)
- [Security](#security)
- [Contributing](#contributing)
- [Credits](#credits)
- [License](#license)

## Getting Started <a name = "getting_started"></a>

### Prerequisites

This package requires the following :

- PHP 8.0 or higher
- Laravel Nova 4

> **Note** If you plan on using this package with an S3 bucket, be mindful to follow the instructions
> for <a href="https://laravel.com/docs/9.x/filesystem#s3-driver-configuration">setting up an S3 storage disk. </a>

### Installing

To get started, you will need to install the following dependencies :

```
composer require bbs-lab/nova-file-manager
```

That's it, you're ready to go!

### Configuration

You may publish the package's configuration by running the following command :

```bash
php artisan vendor:publish --tag="nova-file-manager-config"
```

> **Note** You can find details about the configuration options in the [configuration file section](#configuration-file).

## Usage <a name="usage"></a>

To get yourself started, you need to add the following tool to your `NovaServiceProvider.php`

```php
// NovaServiceProvider.php

use BBSLab\NovaFileManager\NovaFileManager;

class NovaServiceProvider extends NovaApplicationServiceProvider
{
    // ...

    public function tools(): array
    {
        return [
            // ... any other tools
            NovaFileManager::make(),
        ];
    }
}
```

Now that the tool is registered, if you go back to your Nova dashboard, you should see a new navigation entry labeled "
File Manager".

Once you've added the tool, you can start using it.

Go ahead and add a `FileManager` field to your Nova resource.

```php
// app/Nova/User.php

use BBSLab\NovaFileManager\FileManager;

class User extends Resource
{
    // ...

    public function fields(NovaRequest $request): array
    {
        return [
            // ... any other fields
            FileManager::make(__('Avatar'), 'avatar'),
        ];
    }
}
```

You have now successfully added a File Manager field to your resource.

### Advanced usage

#### Multiple selection on the form field

When using the `FileManager` field on your Nova resource, you can instruct the tool to allow multiple selection for your attribute.

By default, the tool will only allow single selection.

You can allow multiple selection by using the `multiple` method to the field. You may limit the number of selected field by using the `limit` method.

```php
// app/Nova/User.php

use BBSLab\NovaFileManager\FileManager;

class User extends Resource
{
    // ...

    public function fields(NovaRequest $request): array
    {
        return [
            // ... any other fields
            FileManager::make(__('Attachements'), 'attachements')
                ->multiple()
                ->limit(10),
        ];
    }
}
```

> **Note** If the limit is set to 1, the field saves the value as a plain string containing the file's path in the specified storage disk. For any value greater than 1, the field saves the value as an array of file paths. You can access these paths easily by setting a cast on your attribute.

```php
// app/Models/User.php

class User extends Authenticatable
{
    // ...

    protected $fillable = [
        'name',
        'email',
        'password',
        'attachements',
    ];

    protected $casts = [
        'attachements' => 'array',
    ];
}

```

#### Validation

When using the field, you can specify the number of files that can be set a value for your resource's attribute.

For that, you can specifically use the following custom rule :

```php
// app/Nova/User.php

use BBSLab\NovaFileManager\FileManager;
use BBSLab\NovaFileManager\Rules\FileLimit;
class User extends Resource
{
    // ...

    public function fields(NovaRequest $request): array
    {
        return [
            // ... any other fields
            FileManager::make(__('Attachements'), 'attachements')
                ->rules(new FileLimit(min: 3, max: 10))
                ->multiple()
                ->limit(10),
        ];
    }
}
```

> **Note** You need to set up your field with `multiple` if you plan on having a minimum value greater than one, and if you expect your field to have more than one file.

#### Saving the disk name alongside the path

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

## Configuration file <a name = "configuration-file"></a>

| Key                                  | Description                                                                              | Type       | Default   | Notes                                                             |
| ------------------------------------ | ---------------------------------------------------------------------------------------- | ---------- | --------- | ----------------------------------------------------------------- |
| `default_disk`                       | The default disk used by the package                                                     | `string`   | `public`  | The default disk must be defined in your `filesystems.php` config |
| `available_disks`                    | Provides a list of available disks to be used by the package                             | `string[]` | -         |                                                                   |
| `show_hidden_files`                  | Toggles whether or not to show files and directories that start with a "dot"             | `bool`     | `false`   |                                                                   |
| `human_readable_size`                | When set to true, the package will display file sizes in a more friendly readable format | `bool`     | `true`    |                                                                   |
| `human_readable_datetime`            | When set to true, the package will display dates with `diffForHumans()`                  | `bool`     | `true`    |                                                                   |
| `file_analysis.enable`               | When set to true, the package will use getID3 to parse metadata from the files           | `bool`     | `true`    |                                                                   |
| `file_analysis.cache.enable`         | When set to true, the package will cache the file analysis result                        | `bool`     | `true`    |                                                                   |
| `file_analysis.cache.ttl_in_seconds` | TTL for analysis caching in seconds                                                      | `int`      | `86400`   |                                                                   |
| `url_signing.enabled`                | When set to true, all the file urls will be signed                                       | `bool`     | `false`   |                                                                   |
| `url_signing.unit`                   | Defines the unit for the expiration time                                                 | `string`   | `minutes` | The expiration time must not exceed 1 week                        |
| `url_signing.value`                  | Defines the value for the expiration time                                                | `int`      | `10`      |                                                                   |

## Authors <a name = "authors"></a>

- [Charaf Rezrazi](https://github.com/crezra)
- [Mikaël Popowicz](https://github.com/mikaelpopowicz)

See also the list of [contributors](https://github.com/bbs-lab/nova-file-manager/contributors) who
participated in this project.

## Screenshots <a name= "screenshots"></a>

<img src="./docs/preview.gif"/>

You can find more screenshots in the `docs` directory.

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Security

If you discover any security related issues, please email paris@big-boss-studio.com instead of using the issue tracker.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Credits

- [getID3() by James Heinrich](https://github.com/JamesHeinrich/getID3)
- [Laravel Chunk Upload](https://github.com/pionl/laravel-chunk-upload)
- [Laravel Pint](https://github.com/laravel/pint)
- [Spatie Laravel Ray](spatie/laravel-ray)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
