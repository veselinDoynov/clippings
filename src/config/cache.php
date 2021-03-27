<?php

$memcachedServers = [
    [
        'host' => env('MEMCACHED_HOST', '127.0.0.1'),
        'port' => env('MEMCACHED_PORT', 11211),
        'weight' => 100,
    ]
];

if ( env('MEMCACHED_HOST_SECONDARY') && env('MEMCACHED_PORT_SECONDARY')) {
    $memcachedServers[] = [
        'host' => env('MEMCACHED_HOST_SECONDARY'),
        'port' => env('MEMCACHED_PORT_SECONDARY'),
        'weight' => 100,
    ];
}

return [

    /*
    |--------------------------------------------------------------------------
    | Default Cache Store
    |--------------------------------------------------------------------------
    |
    | This option controls the default cache connection that gets used while
    | using this caching library. This connection is used when another is
    | not explicitly specified when executing a given caching function.
    |
    | Supported: "apc", "array", "database", "file", "memcached", "redis"
    |
    */

    'default' => env('CACHE_DRIVER', 'file'),

    /*
    |--------------------------------------------------------------------------
    | Cache Stores
    |--------------------------------------------------------------------------
    |
    | Here you may define all of the cache "stores" for your application as
    | well as their drivers. You may even define multiple stores for the
    | same cache driver to group types of items stored in your caches.
    |
    */

    'stores' => [
        'file' => [
            'driver' => 'file',
            'path' => storage_path('framework/cache/data'),
        ],
        'memcached' => [
            'driver' => 'memcached',
            'servers' => $memcachedServers,
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Cache Key Prefix
    |--------------------------------------------------------------------------
    |
    | When utilizing a RAM based store such as APC or Memcached, there might
    | be other applications utilizing the same cache. So, we'll specify a
    | value to get prefixed to all our keys so we can avoid collisions.
    |
    */

    'prefix' => env('CACHE_VAR_PREFIX', 'laravel'),
];
