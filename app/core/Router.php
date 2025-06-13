<?php
require_once __DIR__ . '/Auth.php';
require_once __DIR__ . '/middleware/AuthMiddleware.php';

class Router {
    private $request;
    private $response;
    private $routes = [];
    private $middleware = [];
    private $routeMiddleware = [];
    private $currentGroupPrefix = '';
    private $currentGroupMiddleware = [];
    private $currentRouteParams = [];
    private $patterns = [
        '{id}' => '([0-9]+)',
        '{slug}' => '([a-z0-9-]+)',
        '{token}' => '([a-f0-9]+)'
    ];
    
    public function __construct(Request $request, Response $response) {
        $this->request = $request;
        $this->response = $response;
        
        // Register default middleware
        $this->registerDefaultMiddleware();
    }
    
    protected function registerDefaultMiddleware() {
        // Register auth middleware
        $this->middleware['auth'] = function() {
            return new AuthMiddleware();
        };
    }
    
    public function get($path, $handler, $middleware = []) {
        $this->addRoute('GET', $path, $handler, $middleware);
    }
    
    public function post($path, $handler, $middleware = []) {
        $this->addRoute('POST', $path, $handler, $middleware);
    }
    
    public function put($path, $handler, $middleware = []) {
        $this->addRoute('PUT', $path, $handler, $middleware);
    }
    
    public function patch($path, $handler, $middleware = []) {
        $this->addRoute('PATCH', $path, $handler, $middleware);
    }
    
    public function delete($path, $handler, $middleware = []) {
        $this->addRoute('DELETE', $path, $handler, $middleware);
    }
    
    public function options($path, $handler, $middleware = []) {
        $this->addRoute('OPTIONS', $path, $handler, $middleware);
    }
    
    public function any($path, $handler, $middleware = []) {
        $this->addRoute('ANY', $path, $handler, $middleware);
    }
    
    private function addRoute($method, $path, $handler, $middleware = []) {
        $this->routes[$method][$path] = [
            'handler' => $handler,
            'middleware' => $middleware
        ];
    }
    
    public function group(array $attributes, \Closure $callback) {
        $previousGroupPrefix = $this->currentGroupPrefix ?? '';
        $previousMiddleware = $this->currentGroupMiddleware ?? [];
        
        // Set new group prefix if provided
        if (isset($attributes['prefix'])) {
            $this->currentGroupPrefix = rtrim($previousGroupPrefix . '/' . trim($attributes['prefix'], '/'), '/');
        }
        
        // Merge middleware if provided
        if (isset($attributes['middleware'])) {
            $this->currentGroupMiddleware = array_merge($previousMiddleware, (array)$attributes['middleware']);
        } else {
            $this->currentGroupMiddleware = $previousMiddleware;
        }
        
        // Execute the callback with the group context
        call_user_func($callback, $this);
        
        // Restore previous group context
        $this->currentGroupPrefix = $previousGroupPrefix;
        $this->currentGroupMiddleware = $previousMiddleware;
    }
    
    private function applyGroupToPath($path) {
        if (!empty($this->currentGroupPrefix)) {
            return '/' . trim($this->currentGroupPrefix . '/' . ltrim($path, '/'), '/');
        }
        return $path;
    }
    
    private function applyMiddleware($routeMiddleware) {
        if (!empty($this->currentGroupMiddleware)) {
            $routeMiddleware = array_merge($this->currentGroupMiddleware, (array)$routeMiddleware);
        }
        return array_unique($routeMiddleware);
    }
    
    private function findRoute($method, $path) {
        // First try direct match for the current HTTP method
        if (isset($this->routes[$method][$path])) {
            return [
                'route' => $this->routes[$method][$path],
                'params' => []
            ];
        }
        
        // Then try parameterized routes for the current HTTP method
        if (isset($this->routes[$method])) {
            foreach ($this->routes[$method] as $routePath => $route) {
                $result = $this->matchRoute($routePath, $path, $route);
                if ($result) {
                    return $result;
                }
            }
        }
        
        // If no match found, try 'ANY' method routes
        if ($method !== 'ANY' && isset($this->routes['ANY'])) {
            // Check direct match first
            if (isset($this->routes['ANY'][$path])) {
                return [
                    'route' => $this->routes['ANY'][$path],
                    'params' => []
                ];
            }
            
            // Then check parameterized routes
            foreach ($this->routes['ANY'] as $routePath => $route) {
                $result = $this->matchRoute($routePath, $path, $route);
                if ($result) {
                    return $result;
                }
            }
        }
        
        return null;
    }
    
    private function matchRoute($routePath, $path, $route) {
        $pattern = $this->convertRouteToRegex($routePath);
        if (preg_match($pattern, $path, $matches)) {
            $params = [];
            foreach ($matches as $key => $value) {
                if (is_string($key)) {
                    $params[$key] = $value;
                }
            }
            return [
                'route' => $route,
                'params' => $params
            ];
        }
        return null;
    }
    
    private function convertRouteToRegex($route) {
        $pattern = preg_quote($route, '#');
        
        // Replace route parameters with regex patterns
        foreach ($this->patterns as $param => $regex) {
            $pattern = str_replace(
                '\\' . $param, 
                '(?P' . $param . $regex . ')', 
                $pattern
            );
        }
        
        return '#^' . $pattern . '$#';
    }
    
    public function dispatch() {
        try {
            $method = $this->request->getMethod();
            $path = $this->request->getPath();
            
            // Debug log
            error_log("Dispatching: $method $path");
            
            // Check if the request method is supported
            if (!in_array($method, ['GET', 'POST', 'PUT', 'DELETE', 'PATCH', 'OPTIONS', 'HEAD'])) {
                throw new \RuntimeException("Unsupported HTTP method: $method");
            }
        
            // Check for CSRF token on POST requests
            if ($method === 'POST' && !$this->request->isAjax()) {
                if (!$this->validateCsrfToken()) {
                    $this->response->setStatusCode(403);
                    $this->response->setContent('Invalid CSRF token');
                    $this->response->send();
                    return;
                }
            }
            
            // Find matching route
            $routeInfo = $this->findRoute($method, $path);
            
            if ($routeInfo === null) {
                error_log("No route found for $method $path");
                $this->response->setStatusCode(404);
                $this->response->setContent('Not Found: ' . $path);
                $this->response->send();
                return;
            }
            
            $route = $routeInfo['route'];
            $this->currentRouteParams = $routeInfo['params'];
            
            // Apply group middleware to the route
            $middleware = $this->applyMiddleware($route['middleware'] ?? []);
            
            // Execute middleware
            if (!empty($middleware)) {
                $this->executeMiddleware($middleware);
            }
            
            // Call the route handler
            $this->callHandler($route['handler']);
            
            // Rate limiting
            if ($this->isRateLimited()) {
                $this->response->setStatusCode(429);
                $this->response->setContent('Too many requests');
                $this->response->send();
                return;
            }
        } catch (\Exception $e) {
            $this->handleException($e);
        }
    }
    
    private function executeMiddleware($middlewareNames) {
        foreach ($middlewareNames as $name) {
            if (!isset($this->middleware[$name])) {
                throw new \RuntimeException("Middleware [{$name}] not found");
            }
            
            $middleware = $this->middleware[$name];
            if (is_callable($middleware)) {
                $middleware = $middleware();
            }
            
            $response = $middleware->handle($this->request, function($request) {
                return $this->response;
            });
            
            // If middleware returns a response, send it and stop execution
            if ($response !== null) {
                $response->send();
                exit();
            }
        }
    }
    
    private function callHandler($handler) {
        try {
            if (is_callable($handler)) {
                call_user_func_array($handler, [$this->request, $this->response, $this->currentRouteParams]);
            } elseif (is_string($handler)) {
                $this->callControllerMethod($handler);
            } else {
                throw new \RuntimeException('Invalid route handler');
            }
        } catch (\Exception $e) {
            if (is_string($handler) && strpos($handler, '@') !== false) {
                // Controller@method format
                list($controllerName, $method) = explode('@', $handler);
                $controllerClass = 'App\\Controllers\\' . $controllerName;
                
                error_log("Loading controller: $controllerClass");
                error_log("Method: $method");
                
                if (!class_exists($controllerClass)) {
                    throw new Exception("Controller class not found: $controllerClass");
                }
            }
            
            $this->handleException($e);
        }
    }
    
    private function callControllerMethod($handler) {
        list($controllerName, $methodName) = explode('@', $handler);
        
        // Define possible controller locations and namespaces
        $controllerLocations = [
            // Global namespace
            [
                'class' => $controllerName,
                'file' => __DIR__ . '/../controllers/' . $controllerName . '.php'
            ],
            // App\Controllers namespace
            [
                'class' => 'App\\Controllers\\' . $controllerName,
                'file' => __DIR__ . '/../app/Controllers/' . $controllerName . '.php'
            ],
            // Legacy location
            [
                'class' => $controllerName,
                'file' => __DIR__ . '/../app/controllers/' . $controllerName . '.php'
            ]
        ];
        
        $controllerClass = null;
        
        // Try each possible location
        foreach ($controllerLocations as $location) {
            if (class_exists($location['class'])) {
                $controllerClass = $location['class'];
                break;
            } elseif (file_exists($location['file'])) {
                require_once $location['file'];
                if (class_exists($location['class'])) {
                    $controllerClass = $location['class'];
                    break;
                }
            }
        }
        
        // If controller still not found, throw an exception
        if ($controllerClass === null) {
            $searchedPaths = array_map(function($loc) { return $loc['file']; }, $controllerLocations);
            throw new \RuntimeException(
                "Controller {$controllerName} not found. Searched in: " . 
                implode(', ', $searchedPaths)
            );
        }
        
        try {
            $controller = new $controllerClass($this->request, $this->response);
            
            if (!method_exists($controller, $methodName)) {
                throw new \RuntimeException("Method {$methodName} not found in controller {$controllerClass}");
            }
        } catch (\Exception $e) {
            error_log("Error instantiating controller {$controllerClass}: " . $e->getMessage());
            throw new \RuntimeException("Error loading controller: " . $e->getMessage(), 0, $e);
        }
        
        // Call the controller method with route parameters
        call_user_func_array([$controller, $methodName], array_values($this->currentRouteParams));
    }
    
    private function handleException(\Exception $e) {
        error_log($e->getMessage());
        $this->response->setStatusCode(500);
        $this->response->setContent('An error occurred while processing your request.');
        $this->response->send();
    }

    /**
     * Validate CSRF token
     *
     * @return bool
     */
    private function validateCsrfToken()
    {
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
