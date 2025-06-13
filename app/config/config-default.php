<?php
// Application configuration
return [
    'app' => [
        'name' => 'LAN Install',
        'url' => 'https://lan-install.online',
        'env' => 'production', // or 'development'
    ],
    
    'database' => [
        'host' => 'localhost',
        'name' => '',
        'user' => '',
        'pass' => '',
    ],
    
    'security' => [
        'csrf_token_name' => '_csrf_token',
        'csrf_token_lifetime' => 3600, // 1 hour
        'rate_limiting' => [
            'enabled' => true,
            'requests_per_minute' => 60,
        ],
    ],
    
    'session' => [
        'name' => 'lan_install_sess',
        'lifetime' => 86400, // 1 day
        'path' => '/',
        'domain' => '.lan-install.online',
        'secure' => true,
        'httponly' => true,
        'samesite' => 'Strict',
    ],
];
