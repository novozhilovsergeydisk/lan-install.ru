<?php
class Controller {
    protected $request;
    protected $response;
    
    public function __construct(Request $request, Response $response) {
        $this->request = $request;
        $this->response = $response;
        
        // Start secure session
        $this->startSession();
        
        // Generate CSRF token if not exists
        $this->generateCsrfToken();
    }
    
    protected function view($viewPath, $data = []) {
        $viewFile = __DIR__ . '/../views/' . $viewPath . '.php';
        
        if (!file_exists($viewFile)) {
            throw new Exception("View file $viewFile not found");
        }
        
        // Extract data to variables
        extract($data);
        
        // Start output buffering
        ob_start();
        
        // Include layout
        $layoutFile = __DIR__ . '/../views/layouts/main.php';
        if (file_exists($layoutFile)) {
            $content = $viewFile;
            include $layoutFile;
        } else {
            include $viewFile;
        }
        
        $output = ob_get_clean();
        
        $this->response->setContent($output);
        $this->response->send();
    }
    
    protected function json($data) {
        $this->response->setHeader('Content-Type', 'application/json');
        $this->response->setContent(json_encode($data));
        $this->response->send();
    }
    
    protected function redirect($url, $statusCode = 302) {
        $this->response->setStatusCode($statusCode);
        $this->response->setHeader('Location', $url);
        $this->response->send();
        exit;
    }
    
    private function startSession() {
        // Only start a new session if one isn't already active
        if (session_status() === PHP_SESSION_NONE) {
            $config = require __DIR__ . '/../config/config.php';
            
            session_name($config['session']['name']);
            session_set_cookie_params([
                'lifetime' => $config['session']['lifetime'],
                'path' => $config['session']['path'],
                'domain' => $config['session']['domain'],
                'secure' => $config['session']['secure'],
                'httponly' => $config['session']['httponly'],
                'samesite' => $config['session']['samesite']
            ]);
            
            session_start();
            
            // Regenerate session ID to prevent fixation
            if (empty($_SESSION['initiated'])) {
                session_regenerate_id(true);
                $_SESSION['initiated'] = true;
            }
        }
    }
    
    private function generateCsrfToken() {
        $config = require __DIR__ . '/../config/config.php';
        $tokenName = $config['security']['csrf_token_name'];
        
        if (empty($_SESSION[$tokenName])) {
            $_SESSION[$tokenName] = bin2hex(random_bytes(32));
        }
    }
    
    protected function sanitizeInput($input) {
        if (is_array($input)) {
            return array_map([$this, 'sanitizeInput'], $input);
        }
        
        return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
    }
    
    protected function validateInput($input, $rules) {
        $errors = [];
        
        foreach ($rules as $field => $rule) {
            $value = $input[$field] ?? null;
            $rules = explode('|', $rule);
            
            foreach ($rules as $singleRule) {
                if ($singleRule === 'required' && empty($value)) {
                    $errors[$field][] = "The $field field is required.";
                }
                
                if ($singleRule === 'email' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $errors[$field][] = "The $field must be a valid email address.";
                }
                
                if (strpos($singleRule, 'min:') === 0) {
                    $min = (int) substr($singleRule, 4);
                    if (strlen($value) < $min) {
                        $errors[$field][] = "The $field must be at least $min characters.";
                    }
                }
                
                if (strpos($singleRule, 'max:') === 0) {
                    $max = (int) substr($singleRule, 4);
                    if (strlen($value) > $max) {
                        $errors[$field][] = "The $field may not be greater than $max characters.";
                    }
                }
            }
        }
        
        return $errors;
    }
}
