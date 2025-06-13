<?php
class Request {
    public function getMethod() {
        return $_SERVER['REQUEST_METHOD'];
    }
    
    public function getPath() {
        $path = $_SERVER['REQUEST_URI'] ?? '/';
        $position = strpos($path, '?');
        
        if ($position === false) {
            return $path;
        }
        
        return substr($path, 0, $position);
    }
    
    public function getIp() {
        $ip = $_SERVER['REMOTE_ADDR'];
        
        // Check for forwarded IP (behind proxy)
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            $ip = trim($ips[0]);
        }
        
        return filter_var($ip, FILTER_VALIDATE_IP) ? $ip : '0.0.0.0';
    }
    
    public function isAjax() {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }
    
    public function get($key = null, $default = null) {
        if ($key === null) {
            return $_GET;
        }
        
        return $this->sanitize($_GET[$key] ?? $default);
    }
    
    public function post($key = null, $default = null) {
        if ($key === null) {
            return $_POST;
        }
        
        return $this->sanitize($_POST[$key] ?? $default);
    }
    
    public function getHeaders() {
        return getallheaders();
    }
    
    public function getUserAgent() {
        return $_SERVER['HTTP_USER_AGENT'] ?? '';
    }
    
    public function getReferer() {
        return $_SERVER['HTTP_REFERER'] ?? '';
    }
    
    private function sanitize($value) {
        if (is_array($value)) {
            return array_map([$this, 'sanitize'], $value);
        }
        
        return htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8');
    }
}
