<?php
// Start output buffering
ob_start();

// Enable error reporting
error_reporting(E_ALL);
error_log("Starting test_home.php");
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

// Set default timezone
date_default_timezone_set('Europe/Moscow');

// Define base path
define('BASE_PATH', dirname(__DIR__));

// Require autoloader if exists
if (file_exists(BASE_PATH . '/vendor/autoload.php')) {
    require_once BASE_PATH . '/vendor/autoload.php';
    error_log("Autoloader loaded");
}

// Load local configuration
try {
    $config = require __DIR__ . '/app/config/config-local.php';
    error_log("Configuration loaded");
} catch (Exception $e) {
    die("Error loading config: " . $e->getMessage() . "\n");
}

// Require core files
require_once __DIR__ . '/app/core/Database.php';
require_once __DIR__ . '/app/core/Request.php';
require_once __DIR__ . '/app/core/Response.php';
require_once __DIR__ . '/app/core/Controller.php';
require_once __DIR__ . '/app/core/Auth.php';
require_once __DIR__ . '/app/core/middleware/AuthMiddleware.php';
require_once __DIR__ . '/app/core/Router.php';

// Require controllers
require_once __DIR__ . '/app/controllers/HomeController.php';
require_once __DIR__ . '/app/controllers/AuthController.php';

error_log("All required files included");

// Initialize session with configuration
session_name($config['session']['name']);
session_set_cookie_params([
    'lifetime' => $config['session']['lifetime'],
    'path' => $config['session']['path'],
    'domain' => $config['session']['domain'],
    'secure' => $config['session']['secure'],
    'httponly' => $config['session']['httponly'],
    'samesite' => $config['session']['samesite']
]);

// Start or resume session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Simulate authenticated user
$_SESSION['auth_user'] = [
    'id' => 1,
    'email' => 'test@example.com',
    'role' => 'admin'
];

echo "=== Testing Home Route ===\n\n";

try {
    // Initialize database connection
    $db = Database::getInstance();
    echo "✓ Database connection established\n";
} catch (Exception $e) {
    die("Database connection error: " . $e->getMessage() . "\n");
}

// Create request and response objects
$request = new Request();
$response = new Response();

// Initialize router
$router = new Router($request, $response);

// Add test route for home
$router->get('/home', 'HomeController@index');

// Set request method and URI for testing
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI'] = '/home';
$_SERVER['SERVER_PROTOCOL'] = 'HTTP/1.1';
$_SERVER['HTTP_HOST'] = 'localhost';
$_SERVER['SERVER_PORT'] = '8000';

echo "Testing route: {$_SERVER['REQUEST_METHOD']} {$_SERVER['REQUEST_URI']}\n";

// Dispatch the request
try {
    $router->dispatch();
    $output = ob_get_clean();
    
    if (strpos($output, 'Добро пожаловать') !== false) {
        echo "✓ Home page loaded successfully\n";
    } else {
        echo "⚠ Home page content not found in output\n";
        echo "Output preview:\n" . substr($output, 0, 500) . "...\n";
    }
} catch (Exception $e) {
    ob_end_clean();
    echo "❌ Error processing route: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . " on line " . $e->getLine() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n=== Test Complete ===\n";
