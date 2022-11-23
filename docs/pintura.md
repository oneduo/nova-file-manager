# Pintura image editor

<br>
<img src="./images/pintura.png"/>
<br>

The package provides an integration with Pintura image editor.

This is a cool library made by [Rik Schennink](https://github.com/rikschennink) that provides a full-fledged image
editor right into the file manager, it is beautifully crafted and feature-rich, and we really like and we hope you will
too.

Please note, that this is a **paid** library not included by default in Nova File Manager, and a valid license is
required to use it.

To learn more, and/or purchase a license, please visit
the [Pintura image editor by PQINA](https://pqina.nl/pintura/?affiliate_id=775099219).

::: tip
The provided link is an affiliate, and we will receive a commission if you purchase a license using this link. It is a
form of support for the package, and we really appreciate it.
:::


## Integration

First you need enable Pintura in you `.env` file :

```sh
NOVA_FILE_MANAGER_USE_PINTURA=true
```

::: danger IMPORTANT
You **must** provide your own copy of Pintura assets, once you have purchased a license, you can download the js and css files from your dashboard.
:::

Once you have your Pintura assets, you have to load them into your application, there's many options to do so, for instance you may place your assets in the `public` directory, and appending them by overriding the default `layout.blade.php` :

```php{7,21-30}
<head>
    <!-- ... -->
    @include('nova::partials.meta')

    <!-- Styles -->
    <link rel="stylesheet" href="{{ mix('app.css', 'vendor/nova') }}">
    <link rel="stylesheet" href="{{ asset('pintura.css') }}">
    
    <!-- ... -->
</head>
<body>
    <!-- ... -->

    <!-- Build Nova Instance -->
    <script>
        const config = @json(\Laravel\Nova\Nova::jsonVariables(request()));
        window.Nova = createNovaApp(config)
        Nova.countdown()
    </script>
    
    <script type="module">
      import { appendDefaultEditor } from '/pintura.js'
    
      const editorOptions = {}
    
      window.novaFileManagerEditor = {
        appendDefaultEditor,
        editorOptions,
      }
    </script>

    @if ($scripts = \Laravel\Nova\Nova::availableScripts(request()))
        <!-- Tool Scripts -->
        @foreach ($scripts as $asset)
            <script src="{!! $asset->url() !!}"></script>
        @endforeach
    @endif
    
    <!-- ... -->
</body>
</html>
```

::: danger IMPORTANT
Pintura must be loaded before the File Manager tool script, therefore the script must be placed before the tool scripts foreach loop.
:::

The `editorOptions` object will be used to create the editor instance. You may make your own integration, but make sure
to provide the `window.novaFileManagerEditor` object.

## Passing options to field/tool

You may provide Pintura-specific options to the tool or a resource field :

```php{15-22}
// app/Nova/Project.php

use Oneduo\NovaFileManager\FileManager;
use Oneduo\NovaFileManager\Rules\FileLimit;

class Project extends Resource
{
    // ...

    public function fields(NovaRequest $request): array
    {
        return [
            // ...
            FileManager::make(__('Attachments'), 'attachments')
                ->pinturaOptions([
                    'cropSelectPresetOptions' => [
                        [null, 'Custom'],
                        [1, 'Square'],
                        [16 / 9, '16:9'],
                        [4 / 3, '4:3'],
                    ],                
                ]),
        ];
    }
}

```

The complete list of available options can be found at https://pqina.nl/pintura/docs/v8/api/plugins/
