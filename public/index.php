<?php
// Enable error reporting for development
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

// Set default timezone
date_default_timezone_set('Europe/Moscow');

// Define base path
define('BASE_PATH', dirname(__DIR__));

// Require autoloader if exists
if (file_exists(BASE_PATH . '/vendor/autoload.php')) {
    require_once BASE_PATH . '/vendor/autoload.php';
}

// Require core files
require_once __DIR__ . '/../app/core/Database.php';
require_once __DIR__ . '/../app/core/Request.php';
require_once __DIR__ . '/../app/core/Response.php';
require_once __DIR__ . '/../app/core/Logger.php';
require_once __DIR__ . '/../app/core/Controller.php';
require_once __DIR__ . '/../app/core/Auth.php';
require_once __DIR__ . '/../app/core/Router.php';

// Load configuration
require_once __DIR__ . '/../app/config/config.php';

// Initialize logger
$logger = new Logger();

// Handle the request
try {
    $request = new Request();
    $response = new Response();
    
    // Log the request
    $logger->logRequest($request);
    
    // Initialize router
    $router = new Router($request, $response);
    
    // Load routes
    require_once __DIR__ . '/../app/config/routes.php';
    
    // Dispatch the request
    $router->dispatch();
    
    // Log the response
    $logger->logResponse($response);
    
} catch (Exception $e) {
    $logger->logError($e);
    http_response_code(500);
    echo "<h1>An error occurred</h1>";
    echo "<p><strong>Message:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p><strong>File:</strong> " . htmlspecialchars($e->getFile()) . " on line " . $e->getLine() . "</p>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
}
