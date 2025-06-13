<?php
// Включаем буферизацию вывода
ob_start();

// Включаем отображение ошибок
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

// Устанавливаем временную зону
date_default_timezone_set('Europe/Moscow');

// Определяем базовый путь
define('BASE_PATH', dirname(__DIR__));

// Подключаем автозагрузчик, если есть
if (file_exists(BASE_PATH . '/vendor/autoload.php')) {
    require_once BASE_PATH . '/vendor/autoload.php';
}

// Загружаем конфигурацию
$config = require __DIR__ . '/app/config/config.php';

// Подключаем основные классы
require_once __DIR__ . '/app/core/Database.php';
require_once __DIR__ . '/app/core/Request.php';
require_once __DIR__ . '/app/core/Response.php';
require_once __DIR__ . '/app/core/Controller.php';
require_once __DIR__ . '/app/core/Auth.php';
require_once __DIR__ . '/app/core/middleware/AuthMiddleware.php';
require_once __DIR__ . '/app/core/Router.php';

// Подключаем контроллеры
require_once __DIR__ . '/app/controllers/HomeController.php';
require_once __DIR__ . '/app/controllers/AuthController.php';
require_once __DIR__ . '/app/controllers/ProfileController.php';

// Функция для тестирования маршрута
function testRoute($router, $method, $path, $data = null) {
    $_SERVER['REQUEST_METHOD'] = $method;
    $_SERVER['REQUEST_URI'] = $path;
    
    if ($data !== null && $method === 'POST') {
        $_POST = $data;
        // Генерируем CSRF токен, если его нет
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        $_POST['_csrf_token'] = $_SESSION['csrf_token'];
    }
    
    ob_clean();
    try {
        $router->dispatch();
        $output = ob_get_contents();
        return [
            'success' => true,
            'output' => $output,
            'redirect' => http_response_code() == 302 ? true : false,
            'status' => http_response_code()
        ];
    } catch (Exception $e) {
        return [
            'success' => false,
            'error' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine()
        ];
    }
}

// Функция для вывода результатов теста
function printTestResult($testName, $result) {
    echo "\n=== $testName ===\n";
    if (isset($result['success']) && $result['success']) {
        if ($result['redirect']) {
            echo "✓ Перенаправление на: " . ($_SERVER['HTTP_LOCATION'] ?? 'неизвестно') . "\n";
        } else {
            echo "✓ Успешно (Статус: " . $result['status'] . ")\n";
        }
    } else {
        echo "❌ Ошибка: " . ($result['error'] ?? 'Неизвестная ошибка') . "\n";
        if (isset($result['file'])) {
            echo "Файл: " . $result['file'] . ":" . $result['line'] . "\n";
        }
    }
}

// Инициализируем сессию
session_name($config['session']['name']);
session_set_cookie_params([
    'lifetime' => $config['session']['lifetime'],
    'path' => $config['session']['path'],
    'domain' => $config['session']['domain'],
    'secure' => $config['session']['secure'],
    'httponly' => $config['session']['httponly'],
    'samesite' => $config['session']['samesite']
]);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Очищаем сессию перед началом тестов
$_SESSION = [];
session_destroy();
session_start();

// Создаем объекты запроса и ответа
$request = new Request();
$response = new Response();

// Инициализируем роутер
$router = new Router($request, $response);

// Добавляем тестовые маршруты
require __DIR__ . '/app/config/routes.php';

echo "=== Начало тестирования аутентификации ===\n";

// 1. Тестируем доступ к защищенному маршруту без авторизации
$result = testRoute($router, 'GET', '/home');
printTestResult("1.1. Доступ к защищенному маршруту без авторизации", $result);

// 2. Тестируем загрузку формы входа
$result = testRoute($router, 'GET', '/login');
printTestResult("2.1. Загрузка формы входа", $result);

// 3. Пробуем войти с неверными данными
$loginData = [
    'email' => 'wrong@example.com',
    'password' => 'wrongpassword',
    '_csrf_token' => ''
];
$result = testRoute($router, 'POST', '/login', $loginData);
printTestResult("3.1. Попытка входа с неверными данными", $result);

// 4. Пробуем войти с правильными данными (замените на реальные данные из БД)
$loginData = [
    'email' => 'admin@example.com',  // Замените на реальный email
    'password' => 'password',       // Замените на реальный пароль
    '_csrf_token' => ''
];
$result = testRoute($router, 'POST', '/login', $loginData);
printTestResult("4.1. Вход с правильными данными", $result);

// 5. Проверяем доступ к защищенному маршруту после входа
$result = testRoute($router, 'GET', '/home');
printTestResult("5.1. Доступ к защищенному маршруту после входа", $result);

// 6. Проверяем доступ к профилю
$result = testRoute($router, 'GET', '/profile');
printTestResult("6.1. Доступ к профилю", $result);

// 7. Выход из системы
$result = testRoute($router, 'POST', '/logout');
printTestResult("7.1. Выход из системы", $result);

// 8. Проверяем доступ к защищенному маршруту после выхода
$result = testRoute($router, 'GET', '/home');
printTestResult("8.1. Доступ к защищенному маршруту после выхода", $result);

// Выводим логи сессии
echo "\n=== Логи сессии ===\n";
print_r($_SESSION);

// Выводим логи ошибок, если есть
$errorLog = ini_get('error_log');
if (file_exists($errorLog)) {
    echo "\n=== Последние ошибки из лога ===\n";
    $errors = file($errorLog, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $errors = array_slice($errors, -10); // Показываем последние 10 ошибок
    echo implode("\n", $errors) . "\n";
} else {
    echo "\nФайл лога ошибок не найден: $errorLog\n";
}

echo "\n=== Тестирование завершено ===\n";
