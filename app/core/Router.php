<?php
class Router {
    private $request;
    private $response;
    private $routes = [];
    
    public function __construct(Request $request, Response $response) {
        $this->request = $request;
        $this->response = $response;
    }
    
    public function get($path, $handler) {
        $this->addRoute('GET', $path, $handler);
    }
    
    public function post($path, $handler) {
        $this->addRoute('POST', $path, $handler);
    }
    
    private function addRoute($method, $path, $handler) {
        $this->routes[$method][$path] = $handler;
    }
    
    public function dispatch() {
        $method = $this->request->getMethod();
        $path = $this->request->getPath();
        
        // Check for CSRF token on POST requests
        if ($method === 'POST' && !$this->request->isAjax()) {
            if (!$this->validateCsrfToken()) {
                $this->response->setStatusCode(403);
                $this->response->setContent('Invalid CSRF token');
                $this->response->send();
                return;
            }
        }
        
        // Rate limiting
        if ($this->isRateLimited()) {
            $this->response->setStatusCode(429);
            $this->response->setContent('Too many requests');
            $this->response->send();
            return;
        }
        
        // Find matching route
        if (isset($this->routes[$method][$path])) {
            $handler = $this->routes[$method][$path];
            $this->callHandler($handler);
        } else {
            $this->response->setStatusCode(404);
            // Custom 404 page content
            $this->response->setContent(file_get_contents(__DIR__ . '/../../public/404.html'));
            $this->response->send();
        }
    }
    
    private function callHandler($handler) {
        list($controllerName, $methodName) = explode('@', $handler);
        $controllerClass = ucfirst($controllerName);
        
        if (!class_exists($controllerClass)) {
            $controllerFile = __DIR__ . '/../controllers/' . $controllerClass . '.php';
            if (file_exists($controllerFile)) {
                require_once $controllerFile;
            } else {
                throw new Exception("Controller $controllerClass not found");
            }
        }
        
        $controller = new $controllerClass($this->request, $this->response);
        
        if (!method_exists($controller, $methodName)) {
            throw new Exception("Method $methodName not found in controller $controllerClass");
        }
        
        $controller->$methodName();
    }
    
    private function validateCsrfToken() {
        $config = require __DIR__ . '/../config/config.php';
        $tokenName = $config['security']['csrf_token_name'];
        
        $postToken = $this->request->post($tokenName) ?? '';
        $sessionToken = $_SESSION[$tokenName] ?? '';
        
        if (empty($postToken) || !hash_equals($sessionToken, $postToken)) {
            return false;
        }
        
        return true;
    }
    
    private function isRateLimited() {
        $config = require __DIR__ . '/../config/config.php';
        
        if (!$config['security']['rate_limiting']['enabled']) {
            return false;
        }
        
        $ip = $this->request->getIp();
        $cacheKey = 'rate_limit_' . md5($ip);
        $currentTime = time();
        
        if (!isset($_SESSION[$cacheKey])) {
            $_SESSION[$cacheKey] = [
                'count' => 1,
                'timestamp' => $currentTime
            ];
            return false;
        }
        
        $rateData = $_SESSION[$cacheKey];
        
        if ($currentTime - $rateData['timestamp'] > 60) {
            $_SESSION[$cacheKey] = [
                'count' => 1,
                'timestamp' => $currentTime
            ];
            return false;
        }
        
        $_SESSION[$cacheKey]['count']++;
        
        return $_SESSION[$cacheKey]['count'] > $config['security']['rate_limiting']['requests_per_minute'];
    }
}
