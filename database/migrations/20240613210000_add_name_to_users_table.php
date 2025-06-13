<?php

class AddNameToUsersTable
{
    public function up()
    {
        $db = Database::getInstance();
        
        // Check if column exists
        $result = $db->fetch("
            SELECT column_name 
            FROM information_schema.columns 
            WHERE table_name = 'users' AND column_name = 'name'
        
        
        if (!$result) {
            // Add name column
            $db->query("ALTER TABLE users ADD COLUMN name VARCHAR(255) AFTER id");
            
            // Update existing users with email as name
            $db->query("UPDATE users SET name = SUBSTRING_INDEX(email, '@', 1) WHERE name IS NULL");
        }
    }
    
    public function down()
    {
        $db = Database::getInstance();
        $db->query("ALTER TABLE users DROP COLUMN IF EXISTS name");
    }
}

// Run migration
$migration = new AddNameToUsersTable();
$migration->up();

echo "Migration completed: Added name column to users table\n";
