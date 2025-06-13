<?php
require_once __DIR__ . '/app/core/Database.php';

$email = 'admin@example.com';
$password = 'password123';

// Hash the password
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

try {
    $db = Database::getInstance();
    
    // Check if user already exists
    $existingUser = $db->fetch("SELECT * FROM users WHERE email = :email", ['email' => $email]);
    
    if ($existingUser) {
        echo "User with email {$email} already exists.\n";
        exit;
    }
    
    // Insert new user
    $db->query(
        "INSERT INTO users (email, password_hash) VALUES (:email, :password_hash)",
        [
            'email' => $email,
            'password_hash' => $hashedPassword
        ]
    );
    
    echo "User created successfully!\n";
    echo "Email: {$email}\n";
    echo "Password: {$password}\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
