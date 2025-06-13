<?php
require_once __DIR__ . '/app/core/Database.php';

try {
    $db = Database::getInstance();
    echo "Database connection successful!\n";
    
    // Check if users table exists
    $result = $db->fetch("SELECT to_regclass('public.users') as exists");
    if ($result['exists']) {
        echo "Users table exists.\n";
        // Show first user (for testing)
        $user = $db->fetch("SELECT * FROM users LIMIT 1");
        if ($user) {
            echo "Found user: " . ($user['email'] ?? 'No email') . "\n";
        } else {
            echo "No users found in the database.\n";
        }
    } else {
        echo "Users table does not exist. You need to create it.\n";
        echo "Here's the SQL to create the users table:\n";
        echo "CREATE TABLE users (
    id SERIAL PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);\n";
    }
} catch (Exception $e) {
    echo "Database error: " . $e->getMessage() . "\n";
    if (strpos($e->getMessage(), 'database "lan_install" does not exist') !== false) {
        echo "\nThe database 'lan_install' does not exist. Please create it first with:\n";
        echo "CREATE DATABASE lan_install;\n";
        echo "Then run the SQL above to create the users table.\n";
    }
}
