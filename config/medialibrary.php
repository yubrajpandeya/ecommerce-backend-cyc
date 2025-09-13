<?php

return [
    /**
     * Default disk used to store media. Ensure this matches a disk defined in
     * `config/filesystems.php`. We recommend `public` so files are accessible
     * via the `storage` symlink (php artisan storage:link).
     */
    'disk_name' => env('MEDIA_DISK', env('FILESYSTEM_DISK', 'public')),

    /**
     * Whether to queue conversions. Keep disabled for simplicity locally.
     */
    'queue_conversions' => false,

    /**
     * Image optimizer: many optimizer binaries are not available on Windows or
     * in some shared hosts. Disable by default to avoid runtime errors; you
     * can enable and install the binaries on production servers.
     */
    'image_optimizer' => [
        'enabled' => false,
        'binary_path' => null,
    ],

    // Keep the default conversions directory inside the storage disk.
    'conversions_disk' => env('MEDIA_CONVERSIONS_DISK', null),
];
