# Pintura image editor

<br>
<img src="./images/pintura.png"/>
<br>

The package provides an integration with [Pintura image editor by PQINA](https://pqina.nl/pintura/?affiliate_id=775099219).

## Integration

First you may enable Pintura in you `.env` file :

```sh
NOVA_FILE_MANAGER_USE_PINTURA=true
```

::: danger IMPORTANT
You **must** provide your own copy of Pintura assets
:::

You may override the **Laravel Nova** layout :

```php{14,53-62}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ \Laravel\Nova\Nova::rtlEnabled() ? 'rtl' : 'ltr' }}" class="h-full font-sans antialiased">
<head>
    <meta name="theme-color" content="#fff">
    <meta charset="utf-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width"/>
    <meta name="locale" content="{{ str_replace('_', '-', app()->getLocale()) }}"/>

    @include('nova::partials.meta')

    <!-- Styles -->
    <link rel="stylesheet" href="{{ mix('app.css', 'vendor/nova') }}">
    <link rel="stylesheet" href="{{ asset('pintura.css') }}">

    @if ($styles = \Laravel\Nova\Nova::availableStyles(request()))
    <!-- Tool Styles -->
        @foreach($styles as $asset)
            <link rel="stylesheet" href="{!! $asset->url() !!}">
        @endforeach
    @endif

    <script>
        if (localStorage.novaTheme === 'dark' || (!('novaTheme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark')
        } else {
            document.documentElement.classList.remove('dark')
        }
    </script>
</head>
<body class="min-w-site text-sm font-medium min-h-full text-gray-500 dark:text-gray-400 bg-gray-100 dark:bg-gray-900">
    @inertia
    <div class="relative z-50">
      <div id="notifications" name="notifications"></div>
    </div>
    <div>
      <div id="dropdowns" name="dropdowns"></div>
      <div id="modals" name="modals"></div>
    </div>

    <!-- Scripts -->
    <script src="{{ mix('manifest.js', 'vendor/nova') }}"></script>
    <script src="{{ mix('vendor.js', 'vendor/nova') }}"></script>
    <script src="{{ mix('app.js', 'vendor/nova') }}"></script>

    <!-- Build Nova Instance -->
    <script>
        const config = @json(\Laravel\Nova\Nova::jsonVariables(request()));
        window.Nova = createNovaApp(config)
        Nova.countdown()
    </script>
    
    <script type="module">
      import { appendDefaultEditor } from '/pintura.js'
    
      const editorOptions = {}
    
      window.Nova.config.novaFileManagerEditor = {
        appendEditor: appendDefaultEditor,
        editorOptions,
      }
    </script>

    @if ($scripts = \Laravel\Nova\Nova::availableScripts(request()))
        <!-- Tool Scripts -->
        @foreach ($scripts as $asset)
            <script src="{!! $asset->url() !!}"></script>
        @endforeach
    @endif

    <!-- Start Nova -->
    <script defer>
        Nova.liftOff()
    </script>
</body>
</html>
```

The `editorOptions` object will be used to create the editor instance. You may make your own integration, but make sure to provide the `window.Nova.config.novaFileManagerEditor` object. 

## Passing options to field/tool

You also may provide pintura options to the tool or a field :

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
            // ... any other fields
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
