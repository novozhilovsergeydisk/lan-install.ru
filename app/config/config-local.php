<?php
// Local development configuration
return [
    'app' => [
        'name' => 'LAN Install (Local)',
        'url' => 'http://localhost:8000',
        'env' => 'development',
    ],
    
    'database' => [
        'host' => 'localhost',
        'name' => 'lan_install',
        'user' => 'postgres',
        'pass' => 'postgres',
    ],
    
    'security' => [
        'csrf_token_name' => '_csrf_token',
        'csrf_token_lifetime' => 3600,
        'rate_limiting' => [
            'enabled' => false, // Disable rate limiting in development
            'requests_per_minute' => 1000,
        ],
    ],
    
    'session' => [
        'name' => 'lan_install_local',
        'lifetime' => 86400 * 30, // 30 days
        'path' => '/',
        'domain' => 'localhost',
        'secure' => false, // Set to false for local development
        'httponly' => true,
        'samesite' => 'Lax', // More permissive for local development
    ],
];
