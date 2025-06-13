<?php
class AuthController extends Controller {
    private $auth;
    
    public function __construct($request = null, $response = null) {
        parent::__construct($request, $response);
        $this->auth = new Auth();
    }
    
    public function showLoginForm() {
        // If user is already logged in, redirect to home
        if ($this->auth->check()) {
            $this->redirect('/home');
            return;
        }
        
        $config = require __DIR__ . '/../config/config.php';
        $tokenName = $config['security']['csrf_token_name'];
        
        // Generate CSRF token if it doesn't exist
        if (empty($_SESSION[$tokenName])) {
            $_SESSION[$tokenName] = bin2hex(random_bytes(32));
        }
        
        $data = [
            'title' => 'Вход в систему',
            'csrf_token' => $_SESSION[$tokenName],
            'old' => $_SESSION['old_input'] ?? [],
            'errors' => $_SESSION['form_errors'] ?? []
        ];
        
        // Clear old input and errors after displaying them
        unset($_SESSION['old_input'], $_SESSION['form_errors']);
        
        $this->view('auth/login', $data);
    }
    
    public function login() {
        $input = $this->request->post();
        
        // Store old input for form repopulation
        $_SESSION['old_input'] = $input;
        
        $rules = [
            'email' => 'required|email',
            'password' => 'required|min:6',
            '_csrf_token' => 'required'
        ];
        
        $errors = $this->validateInput($input, $rules);
        
        // Verify CSRF token
        $config = require __DIR__ . '/../config/config.php';
        $tokenName = $config['security']['csrf_token_name'];
        
        if (!isset($input['_csrf_token']) || $input['_csrf_token'] !== ($_SESSION[$tokenName] ?? '')) {
            $errors['_csrf_token'] = ['Неверный токен безопасности. Пожалуйста, обновите страницу.'];
        }
        
        if (!empty($errors)) {
            $_SESSION['form_errors'] = $errors;
            $this->redirect('/login');
            return;
        }
        
        // Attempt to authenticate user
        if ($this->auth->attempt($input['email'], $input['password'])) {
            // Clear old input on successful login
            unset($_SESSION['old_input']);
            
            // Regenerate session ID to prevent session fixation
            if (session_status() === PHP_SESSION_ACTIVE) {
                session_regenerate_id(true);
            }
            
            // Redirect to intended URL or home
            $redirectTo = $_SESSION['redirect_after_login'] ?? '/home';
            unset($_SESSION['redirect_after_login']);
            
            $this->redirect($redirectTo);
            return;
        }
        
        // Authentication failed
        $_SESSION['form_errors'] = [
            'email' => ['Неверный email или пароль.']
        ];
        $this->redirect('/login');
    }
    
    public function logout() {
        $this->auth->logout();
        $this->redirect('/login');
    }
}
