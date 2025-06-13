<?php
class Auth {
    private $db;
    private $sessionKey = 'auth_user';
    private $config;
    
    public function __construct() {
        $this->db = Database::getInstance();
        $this->config = require __DIR__ . '/../config/config-default.php';
        $this->startSession();
    }
    
    private function startSession() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start([
                'name' => $this->config['session']['name'],
                'cookie_lifetime' => $this->config['session']['lifetime'],
                'cookie_path' => $this->config['session']['path'],
                'cookie_domain' => $this->config['session']['domain'],
                'cookie_secure' => $this->config['session']['secure'],
                'cookie_httponly' => $this->config['session']['httponly'],
                'cookie_samesite' => $this->config['session']['samesite']
            ]);
        }
    }
    
    public function attempt($email, $password) {
        $user = $this->db->fetch(
            'SELECT * FROM users WHERE email = :email',
            ['email' => $email]
        );
        
        if ($user && password_verify($password, $user['password_hash'])) {
            $this->login($user);
            return true;
        }
        
        return false;
    }
    
    public function login($user) {
        // Remove sensitive data before storing in session
        unset($user['password_hash']);
        $_SESSION[$this->sessionKey] = $user;
        return true;
    }
    
    public function logout() {
        unset($_SESSION[$this->sessionKey]);
        session_destroy();
        return true;
    }
    
    public function check() {
        return isset($_SESSION[$this->sessionKey]);
    }
    
    public function user() {
        return $_SESSION[$this->sessionKey] ?? null;
    }
    
    public function id() {
        return $_SESSION[$this->sessionKey]['id'] ?? null;
    }
    
    public function isAdmin() {
        return ($_SESSION[$this->sessionKey]['role'] ?? null) === 'admin';
    }
    
    public function isInstaller() {
        return ($_SESSION[$this->sessionKey]['role'] ?? null) === 'installer';
    }
}
