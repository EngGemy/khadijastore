<?php

return [
    'enabled' => env('AUDITING_ENABLED', true),
    'implementation' => OwenIt\Auditing\Models\Audit::class,
    'user' => [
        'morph_prefix' => 'user',
        'guards' => ['web'],
        'resolver' => OwenIt\Auditing\Resolvers\UserResolver::class,
    ],
    'resolvers' => [
        'ip_address' => OwenIt\Auditing\Resolvers\IpAddressResolver::class,
        'user_agent' => OwenIt\Auditing\Resolvers\UserAgentResolver::class,
        'url' => OwenIt\Auditing\Resolvers\UrlResolver::class,
    ],
    'events' => ['created', 'updated', 'deleted', 'restored'],
    'strict' => false,
    'threshold' => 0,
    'driver' => 'database',
    'drivers' => [
        'database' => [
            'table' => 'audits',
            'connection' => null,
        ],
    ],
    'queue' => ['enable' => false],
    'console' => false, // لا توثّق عمليات الـ CLI/seeders
];
