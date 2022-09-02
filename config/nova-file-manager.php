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
        // 's3',
        // 'ftp',
        // ... more disks
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
    'show_hidden_files' => env('NOVA_FILE_MANAGER_SHOW_HIDDEN_FILES', false),

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
    'human_readable_size' => env('NOVA_FILE_MANAGER_HUMAN_READABLE_SIZE', true),

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
    'human_readable_datetime' => env('NOVA_FILE_MANAGER_HUMAN_READABLE_DATETIME', true),

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
        'enabled' => env('NOVA_FILE_MANAGER_ENABLE_FILE_ANALYSIS', true),
        'cache' => [
            'enable' => env('NOVA_FILE_MANAGER_FILE_ANALYSIS_CACHE_ENABLE', true),
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
    | Should extend \Bbs\NovaFileManager\Entities\Entity::class
    |
    */
    'entities' => [
        'image' => \BBSLab\NovaFileManager\Entities\Image::class,
        'video' => \BBSLab\NovaFileManager\Entities\Video::class,
        'default' => \BBSLab\NovaFileManager\Entities\File::class,
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
        'enabled' => env('NOVA_FILE_MANAGER_ENABLED_URL_SIGNING', false),
        'unit' => 'minutes',
        'value' => 10,
    ],
];
