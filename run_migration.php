<?php
// Include required files
require_once __DIR__ . '/app/core/Database.php';

// Load configuration
$config = require __DIR__ . '/app/config/config-default.php';

// Initialize database connection
try {
    $db = Database::getInstance($config['database']);
    
    // Check if name column exists
    $result = $db->fetch("
        SELECT column_name 
        FROM information_schema.columns 
        WHERE table_name = 'users' AND column_name = 'name'
    ");
    
    if (!$result) {
        echo "Adding name column to users table...\n";
        
        // Add name column
        $db->query("ALTER TABLE users ADD COLUMN name VARCHAR(255)");
        
        // Update existing users with email as name (using PostgreSQL's split_part)
        $db->query("UPDATE users SET name = split_part(email, '@', 1) WHERE name IS NULL");
        
        echo "Migration completed: Added name column to users table\n";
    } else {
        echo "Name column already exists in users table\n";
    }
    
} catch (Exception $e) {
    die("Error: " . $e->getMessage() . "\n");
}
