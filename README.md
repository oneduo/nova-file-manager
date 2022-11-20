<img src="./docs/images/banner.png"/>
<img src="./docs/images/tiles.png"/>


<a href="https://oneduo.github.io/nova-file-manager/" taget="_blank"><img src="./docs/images/documentation.png"/></a>
# Nova File Manager

<div align="left">

![Status](https://img.shields.io/badge/status-active-success.svg)
[![License](https://img.shields.io/badge/license-MIT-blue.svg)](/LICENSE)
![PHP](https://img.shields.io/badge/PHP-8-blue.svg)
![Laravel Nova](https://img.shields.io/badge/laravel%2Fnova-4-cyan.svg)
[![Latest Version on Packagist](https://img.shields.io/packagist/v/oneduo/nova-file-manager.svg)](https://packagist.org/packages/oneduo/nova-file-manager)
[![Total Downloads](https://img.shields.io/packagist/dt/bbs-lab/nova-file-manager.svg)](https://packagist.org/packages/bbs-lab/nova-file-manager)
[![Downloads](https://img.shields.io/packagist/dt/oneduo/nova-file-manager.svg)](https://packagist.org/packages/oneduo/nova-file-manager)
[![Run tests](https://github.com/oneduo/nova-file-manager/actions/workflows/tests.yml/badge.svg?branch=main)](https://github.com/oneduo/nova-file-manager/actions/workflows/tests.yml)
</div>

---

<p>
A file manager tool and field for Laravel Nova. Beautifully designed, and customizable, this tool will provide a plug'n'play solution for your file management needs.

**Features :**

⚡️ Blazing fast  
📦️ Laravel Nova 4 compatible  
💅 Built with Tailwindcss 3, Vue 3 and Pinia  
🪨 Rock solid codebase built with Typescript  
💽 Multi disk and filesystem support  
🧩 Supports chunk and resumable uploads  
🔧 Various customization and configuration options  
🔍 A performant local search feature with Spotlight  
🤹‍ Can save multiple assets from the same field  
🔐 Access control and authorization gates  
✂️ Built-in crop tool and image editor  
📇 Built-in PDF viewer  
🗂️ Drag and drop upload, with entire folder upload support  
🗃️ Supports unzipping files  
🚩 Onboarding tour for new users
</p>




<img src="./docs/images/support.png"/>

## Table of Contents

- [Getting Started](#getting_started)
    - [Prerequisites](#prerequisites)
    - [Installing](#installing)
    - [Configuration](#configuration)
- [Usage](#usage)
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
composer require oneduo/nova-file-manager
```

That's it, you're ready to go!

### Configuration

You may publish the package's configuration by running the following command :

```bash
php artisan vendor:publish --tag="nova-file-manager-config"
```

> **Note** You can find details about the configuration options in
> the [configuration file section](#configuration-file).

## Usage <a name="usage"></a>

To get yourself started, you need to add the following tool to your `NovaServiceProvider.php`

```php
// NovaServiceProvider.php

use Oneduo\NovaFileManager\NovaFileManager;

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

use Oneduo\NovaFileManager\FileManager;

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

## Configuration file <a name = "configuration-file"></a>

| Key                                  | Description                                                                              | Type       | Default   | Notes                                                             |
|--------------------------------------|------------------------------------------------------------------------------------------|------------|-----------|-------------------------------------------------------------------|
| `default_disk`                       | The default disk used by the package                                                     | `string`   | `public`  | The default disk must be defined in your `filesystems.php` config |
| `available_disks`                    | Provides a list of available disks to be used by the package                             | `string[]` | -         |                                                                   |
| `show_hidden_files`                  | Toggles whether or not to show files and folders that start with a "dot"                 | `bool`     | `false`   |                                                                   |
| `human_readable_size`                | When set to true, the package will display file sizes in a more friendly readable format | `bool`     | `true`    |                                                                   |
| `human_readable_datetime`            | When set to true, the package will display dates with `diffForHumans()`                  | `bool`     | `true`    |                                                                   |
| `file_analysis.enable`               | When set to true, the package will use getID3 to parse metadata from the files           | `bool`     | `true`    |                                                                   |
| `file_analysis.cache.enable`         | When set to true, the package will cache the file analysis result                        | `bool`     | `true`    |                                                                   |
| `file_analysis.cache.ttl_in_seconds` | TTL for analysis caching in seconds                                                      | `int`      | `86400`   |                                                                   |
| `url_signing.enabled`                | When set to true, all the file urls will be signed                                       | `bool`     | `false`   |                                                                   |
| `url_signing.unit`                   | Defines the unit for the expiration time                                                 | `string`   | `minutes` | The expiration time must not exceed 1 week                        |
| `url_signing.value`                  | Defines the value for the expiration time                                                | `int`      | `10`      |                                                                   |

For a full list of updated configuration options, please refer to the full documentation
at https://oneduo.github.io/nova-file-manager/configuration.html

## Authors <a name = "authors"></a>

- [Charaf Rezrazi](https://github.com/rezrazi)
- [Mikaël Popowicz](https://github.com/mikaelpopowicz)

See also the list of [contributors](https://github.com/oneduo/nova-file-manager/contributors) who
participated in this project.

## Screenshots <a name= "screenshots"></a>

You can find more screenshots here https://oneduo.github.io/nova-file-manager/screenshots.html.

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Security

If you discover any security related issues, please email charaf@rezrazi.fr instead of using the issue tracker.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Credits

- [getID3() by James Heinrich](https://github.com/JamesHeinrich/getID3)
- [Laravel Chunk Upload](https://github.com/pionl/laravel-chunk-upload)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
