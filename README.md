<img src="./docs/banner.png"/>
<h3 align="left">Nova File Manager</h3>

<div align="left">

[![Status](https://img.shields.io/badge/status-active-success.svg)]()
[![License](https://img.shields.io/badge/license-MIT-blue.svg)](/LICENSE)
[![PHP](https://img.shields.io/badge/PHP-8-blue.svg)]()
[![Laravel Nova](https://img.shields.io/badge/laravel%2Fnova-4-cyan.svg)]()


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
</p>

## Table of Contents

- [Getting Started](#getting_started)
- [Usage](#usage)
- [Configuration](#configuration-file)
- [Contributing](../CONTRIBUTING.md)
- [Authors](#authors)

## Getting Started <a name = "getting_started"></a>

### Prerequisites

This package requires the following :

- PHP 8.0 or higher
- Laravel Nova 4

> **Note** If you plan on using this package with an S3 bucket, be mindful to follow the instructions
for <a href="https://laravel.com/docs/9.x/filesystem#s3-driver-configuration">setting up an S3 storage disk. </a>

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

| Key                       | Description                                                                              | Type       | Default   | Notes                                                             |
|---------------------------|------------------------------------------------------------------------------------------|------------|-----------|-------------------------------------------------------------------|
| `default_disk`            | The default disk used by the package                                                     | `string`   | `public`  | The default disk must be defined in your `filesystems.php` config |
| `available_disks`         | Provides a list of available disks to be used by the package                             | `string[]` | -         |                                                                   |
| `show_hidden_files`       | Toggles whether or not to show files and directories that start with a "dot"             | `bool`     | `false`   |                                                                   |
| `human_readable_size`     | When set to true, the package will display file sizes in a more friendly readable format | `bool`     | `true`    |                                                                   |
| `human_readable_datetime` | When set to true, the package will display dates with `diffForHumans()`                  | `bool`     | `true`    |                                                                   |
| `enable_file_analysis`    | When set to true, the package will use getID3 to parse metadata from the files           | `bool`     | `true`    |                                                                   |
| `url_signing.enabled`     | When set to true, all the file urls will be signed                                       | `bool`     | `false`   |                                                                   |
| `url_signing.unit`        | Defines the unit for the expiration time                                                 | `string`   | `minutes` | The expiration time must not exceed 1 week                        |
| `url_signing.value`       | Defines the value for the expiration time                                                | `int`      | `10`      |                                                                   |

## Authors <a name = "authors"></a>

- [Charaf Rezrazi](https://github.com/crezra)
- [Mikaël Popowicz](https://github.com/mikaelpopowicz)

See also the list of [contributors](https://github.com/bbs-lab/nova-file-manager/contributors) who
participated in this project.

