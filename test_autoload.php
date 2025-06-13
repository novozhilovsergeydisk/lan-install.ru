<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Define base path
define('BASE_PATH', dirname(__DIR__));

// Require autoloader if exists
if (file_exists(BASE_PATH . '/vendor/autoload.php')) {
    require_once BASE_PATH . '/vendor/autoload.php';
    echo "✓ Autoloader loaded\n";
} else {
    die("✗ Autoloader not found\n");
}

// Test if HomeController can be loaded
echo "Testing HomeController...\n";

// Try to load HomeController from different namespaces
$controllerClasses = [
    'HomeController',
    'App\\Controllers\\HomeController',
    '\\App\\Controllers\\HomeController'
];

$found = false;
foreach ($controllerClasses as $class) {
    if (class_exists($class)) {
        echo "✓ Found class: $class\n";
        $found = true;
        break;
    } else {
        echo "✗ Not found: $class\n";
    }
}

if (!$found) {
    echo "\nTrying to manually include HomeController.php...\n";
    $controllerFile = __DIR__ . '/app/controllers/HomeController.php';
    if (file_exists($controllerFile)) {
        require_once $controllerFile;
        if (class_exists('HomeController')) {
            echo "✓ Successfully loaded HomeController from $controllerFile\n";
            $found = true;
        } else {
            echo "✗ File exists but class HomeController not found in $controllerFile\n";
        }
    } else {
        echo "✗ Controller file not found: $controllerFile\n";
    }
}

if ($found) {
    echo "\n✅ HomeController is properly loaded\n";
} else {
    echo "\n❌ Failed to load HomeController\n";
}
