<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Default Disk
    |--------------------------------------------------------------------------
    |
    | Can be used to set the default disk used by the tool.
    | When no disk is selected, the tool will use the default public disk.
    |
    | default: public
    */
    'default_disk' => env('NOVA_FILE_MANAGER_DISK', 'public'),

    /*
    |--------------------------------------------------------------------------
    | Available disks
    |--------------------------------------------------------------------------
    |
    | Can be used to specify the filesystem disks that can be available in the
    | tool. Note that the default disk (in this case "PUBLIC") is required to
    | be in this array.
    |
    | The disks should be defined in the filesystems.php config.
    |
    */
    'available_disks' => [
        'public',
        's3',
        // 'ftp',
        // ... more disks
    ],

    'paginate_options' => [
        'paginate_default' => (int) env('NOVA_FILE_MANAGER_PAGINATE_DEFAULT', 10),
        'pagination_start' => (int) env('NOVA_FILE_MANAGER_PAGINATE_START', 10),
        'pagination_end' => (int) env('NOVA_FILE_MANAGER_PAGINATE_END', 50),
        'pagination_step' => (int) env('NOVA_FILE_MANAGER_PAGINATE_STEP', 10),
    ],

    /*
    |--------------------------------------------------------------------------
    | Show hidden files
    |--------------------------------------------------------------------------
    |
    | Toggle whether the tool should show the files and directories which name
    | starts with "."
    |
    | default: false
    */
    'show_hidden_files' => (bool) env('NOVA_FILE_MANAGER_SHOW_HIDDEN_FILES', false),

    /*
    |--------------------------------------------------------------------------
    | Human readable file size
    |--------------------------------------------------------------------------
    |
    | If set to true, the tool will display the file size in a parsed and more
    | readable format. Otherwise, it will display the raw byte size.
    |
    | default: true
    */
    'human_readable_size' => (bool) env('NOVA_FILE_MANAGER_HUMAN_READABLE_SIZE', true),

    /*
    |--------------------------------------------------------------------------
    | Human readable timestamps
    |--------------------------------------------------------------------------
    |
    | If set to true, the tool will display datetime string in a human-readable
    | difference format. Otherwise, it will display the regular datetime value.
    |
    | default: true
    */
    'human_readable_datetime' => (bool) env('NOVA_FILE_MANAGER_HUMAN_READABLE_DATETIME', true),

    /*
    |--------------------------------------------------------------------------
    | File analysis
    |--------------------------------------------------------------------------
    |
    | If set to true, the tool will analyze the file in order to retrieve
    | metadata using getID3. This can be cost-expensive and can introduce
    | performance issues. You may disable this option if not needed.
    |
    | default: true
    */

    'file_analysis' => [
        'enabled' => (bool) env('NOVA_FILE_MANAGER_FILE_ANALYSIS_ENABLED', true),
        'cache' => [
            'enabled' => (bool) env('NOVA_FILE_MANAGER_FILE_ANALYSIS_CACHE_ENABLED', true),
            'ttl_in_seconds' => env('NOVA_FILE_MANAGER_FILE_ANALYSIS_CACHE_TTL_IN_SECONDS', 60 * 60 * 24),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Entities map
    |--------------------------------------------------------------------------
    |
    | Here you can override or define new entity types that can be used to map
    | the files in your storage.
    |
    | Should extend \Oneduo\NovaFileManager\Entities\Entity::class
    |
    */
    'entities' => [
        'image' => \Oneduo\NovaFileManager\Entities\Image::class,
        'video' => \Oneduo\NovaFileManager\Entities\Video::class,
        'default' => \Oneduo\NovaFileManager\Entities\File::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | URL Signing
    |--------------------------------------------------------------------------
    |
    | When using a cloud filesystem disk (e.g. S3), you may wish to provide
    | signed url through the tool. You can enable the setting, and adjust the
    | signing configuration.
    |
    | Uses: Storage::temporaryUrl()
    */
    'url_signing' => [
        'enabled' => (bool) env('NOVA_FILE_MANAGER_URL_SIGNING_ENABLED', false),
        'unit' => 'minutes',
        'value' => 10,
    ],

    /*
    |--------------------------------------------------------------------------
    | Update checker
    |--------------------------------------------------------------------------
    |
    | The tool provides a handy update checker that will notify you when a new
    | version is available. You can disable it if you don't want to receive
    | these notifications.
    |
    */
    'update_checker' => [
        'enabled' => (bool) env('NOVA_FILE_MANAGER_UPDATE_CHECKER_ENABLED', true),
        'ttl_in_days' => (int) env('NOVA_FILE_MANAGER_UPDATE_CHECKER_TTL_IN_DAYS', 1),
    ],

    /*
    |--------------------------------------------------------------------------
    | Onboarding tour
    |--------------------------------------------------------------------------
    |
    | You can enable a handy onboarding tour to guide you or your users through
    | the tool. You can disable it if you don't want to show it.
    |
    | default: true
    |
    */
    'tour' => [
        'enabled' => (bool) env('NOVA_FILE_MANAGER_TOUR_ENABLED', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Pintura by PQINA
    |--------------------------------------------------------------------------
    |
    | Enable Pintura editor by PQINA.
    |
    */
    'use_pintura' => (bool) env('NOVA_FILE_MANAGER_USE_PINTURA', false),

    /*
    |--------------------------------------------------------------------------
    | File Manager Path
    |--------------------------------------------------------------------------
    |
    | This is the URI path where File Manager will be accessible from.
    | Feel free to change this path to anything you like.
    |
    */

    'path' => '/nova-file-manager',

    /*
    |--------------------------------------------------------------------------
    | Upload replace existing
    |--------------------------------------------------------------------------
    |
    | Toggle whether an upload with an existing file name should replace
    | the existing file or not.
    |
    */

    'upload_replace_existing' => (bool) env('NOVA_FILE_MANAGER_UPLOAD_REPLACE_EXISTING', false),

];
