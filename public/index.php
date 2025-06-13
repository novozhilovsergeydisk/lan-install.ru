<?php
require_once __DIR__ . '/../app/core/Router.php';
require_once __DIR__ . '/../app/core/Controller.php';
require_once __DIR__ . '/../app/core/Database.php';
require_once __DIR__ . '/../app/core/Request.php';
require_once __DIR__ . '/../app/core/Response.php';
require_once __DIR__ . '/../app/core/Logger.php';

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
    echo "An error occurred. Please try again later.";
}
