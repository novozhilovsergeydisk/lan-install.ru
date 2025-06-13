<?php
// Start output buffering
ob_start();

// Enable error reporting
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

// Load local configuration
$config = require __DIR__ . '/app/config/config-local.php';

// Require core files
require_once __DIR__ . '/app/core/Database.php';
require_once __DIR__ . '/app/core/Request.php';
require_once __DIR__ . '/app/core/Response.php';
require_once __DIR__ . '/app/core/Controller.php';
require_once __DIR__ . '/app/core/Auth.php';
require_once __DIR__ . '/app/core/Router.php';
require_once __DIR__ . '/app/core/middleware/AuthMiddleware.php';
require_once __DIR__ . '/app/controllers/AuthController.php';

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

echo "=== Начало тестирования маршрутизации и аутентификации ===\n\n";

try {
    // Initialize database connection
    $db = Database::getInstance();
    echo "✓ Подключение к базе данных успешно установлено\n";
} catch (Exception $e) {
    die("Ошибка подключения к базе данных: " . $e->getMessage() . "\n");
}

// Create request and response objects
$request = new Request();
$response = new Response();

// Initialize router
$router = new Router($request, $response);

// 1. Check if controller exists
if (!class_exists('AuthController')) {
    die("Ошибка: Класс AuthController не найден\n");
}

echo "✓ AuthController существует\n";

// 2. Check if showLoginForm method exists
$authController = new AuthController($request, $response);
if (!method_exists($authController, 'showLoginForm')) {
    die("Ошибка: Метод showLoginForm не найден в AuthController\n");
}

echo "✓ Метод showLoginForm существует\n\n";

echo "=== Тестирование маршрутизации ===\n\n";

// 3. Test route handling
$testPath = '/login';
$testMethod = 'GET';

echo "Тестируем маршрут: $testMethod $testPath\n";

// Add test route
$router->get($testPath, 'AuthController@showLoginForm');

// Set request method and URI
$_SERVER['REQUEST_METHOD'] = $testMethod;
$_SERVER['REQUEST_URI'] = $testPath;
$_SERVER['SERVER_PROTOCOL'] = 'HTTP/1.1';
$_SERVER['HTTP_HOST'] = 'localhost';
$_SERVER['SERVER_PORT'] = '8000';

// Dispatch the request
try {
    $router->dispatch();
    $output = ob_get_clean();
    
    // Check if the output contains the login form
    if (strpos($output, '<form') !== false && strpos($output, 'login') !== false) {
        echo "✓ Форма входа успешно отображена\n";
    } else {
        echo "⚠ Форма входа не обнаружена в выводе\n";
        echo "Вывод:\n" . substr($output, 0, 500) . "...\n";
    }
} catch (Exception $e) {
    ob_end_clean();
    echo "❌ Ошибка при обработке маршрута: " . $e->getMessage() . "\n";
    echo "Ошибка при обработке маршрута: " . $e->getMessage() . "\n";
    echo "Файл: " . $e->getFile() . " на строке " . $e->getLine() . "\n";
    echo "Стек вызовов: " . $e->getTraceAsString() . "\n";
}

echo "\n=== Тестирование завершено ===\n";
