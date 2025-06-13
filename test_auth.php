<?php
// Include required files
require_once __DIR__ . '/app/core/Database.php';
require_once __DIR__ . '/app/core/Controller.php';
require_once __DIR__ . '/app/core/Auth.php';
require_once __DIR__ . '/app/controllers/AuthController.php';

// Load configuration
$config = require __DIR__ . '/app/config/config-default.php';

// Initialize database connection
try {
    Database::getInstance($config['database']);
} catch (Exception $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Mock Request and Response classes for testing
class Request {
    private $data = [];
    
    public function post($key = null, $default = null) {
        if ($key === null) {
            return $this->data;
        }
        return $this->data[$key] ?? $default;
    }
    
    public function setPostData($data) {
        $this->data = $data;
    }
}

class Response {
    private $statusCode = 200;
    private $headers = [];
    private $content = '';
    
    public function setStatusCode($code) {
        $this->statusCode = $code;
        http_response_code($code);
        return $this;
    }
    
    public function setHeader($name, $value) {
        $this->headers[$name] = $value;
        return $this;
    }
    
    public function setContent($content) {
        $this->content = $content;
        return $this;
    }
    
    public function send() {
        // Send headers
        foreach ($this->headers as $name => $value) {
            header("$name: $value");
        }
        
        // Send content
        echo $this->content;
        
        // Clear the buffer
        if (ob_get_level() > 0) {
            ob_end_flush();
        }
    }
    
    public function redirect($url, $statusCode = 302) {
        $this->setStatusCode($statusCode);
        $this->setHeader('Location', $url);
        $this->send();
        exit;
    }
}

// Start or resume session with proper configuration
if (session_status() === PHP_SESSION_NONE) {
    session_name($config['session']['name'] ?? 'PHPSESSID');
    session_set_cookie_params([
        'lifetime' => $config['session']['lifetime'] ?? 0,
        'path' => $config['session']['path'] ?? '/',
        'domain' => $config['session']['domain'] ?? '',
        'secure' => $config['session']['secure'] ?? false,
        'httponly' => $config['session']['httponly'] ?? true,
        'samesite' => $config['session']['samesite'] ?? 'Lax'
    ]);
    session_start();
}

// Start output buffering
ob_start();

// Create instances
$request = new Request();
$response = new Response();
$controller = new AuthController($request, $response);

// Set test data
$testEmail = 'admin@example.com';
$testPassword = 'password123';

echo "Starting authentication tests...\n";

// Test 1: Show login form
echo "\n=== Test 1: Show login form ===\n";
$controller->showLoginForm();
$output = ob_get_clean();
if (strpos($output, 'Вход в систему') !== false) {
    echo "✓ Login form displayed successfully\n";
} else {
    echo "✗ Failed to display login form\n";
}

// Test 2: Test login with invalid credentials
echo "\n=== Test 2: Login with invalid credentials ===\n";
ob_start();
$request->setPostData([
    'email' => 'nonexistent@example.com',
    'password' => 'wrongpassword',
    '_csrf_token' => 'testtoken'
]);

// Set a test CSRF token
$_SESSION['csrf_token'] = 'testtoken';

// Run login test
$controller->login();
$output = ob_get_clean();

if (!empty($_SESSION['form_errors'])) {
    echo "✓ Login with invalid credentials failed as expected\n";
} else {
    echo "✗ Login with invalid credentials should have failed\n";
}

// Test 3: Test login with valid credentials
echo "\n=== Test 3: Login with valid credentials ===\n";
ob_start();
$request->setPostData([
    'email' => $testEmail,
    'password' => $testPassword,
    '_csrf_token' => 'testtoken'
]);

// Run login test
$controller->login();
$output = ob_get_clean();

if (isset($_SESSION['user_id'])) {
    echo "✓ Login successful\n";
    
    // Test 4: Test logout
    echo "\n=== Test 4: Logout ===\n";
    ob_start();
    $controller->logout();
    $output = ob_get_clean();
    
    if (empty($_SESSION['user_id'])) {
        echo "✓ Logout successful\n";
    } else {
        echo "✗ Logout failed\n";
    }
} else {
    echo "✗ Login with valid credentials failed\n";
    if (!empty($_SESSION['form_errors'])) {
        echo "  Error details: ";
        print_r($_SESSION['form_errors']);
        echo "\n";
    }
}

echo "\n=== Test summary ===\n";
echo "All tests completed.\n";

// Clean up
session_destroy();
