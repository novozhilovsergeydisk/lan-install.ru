<?php
class AuthController extends Controller {
    public function showLoginForm() {
        $config = require __DIR__ . '/../config/config.php';
        $tokenName = $config['security']['csrf_token_name'];
        
        $data = [
            'title' => 'Login',
            'csrf_token' => $_SESSION[$tokenName]
        ];
        
        $this->view('auth/login', $data);
    }
    
    public function login() {
        $input = $this->request->post();
        $sanitizedInput = $this->sanitizeInput($input);
        
        $rules = [
            'email' => 'required|email',
            'password' => 'required|min:6'
        ];
        
        $errors = $this->validateInput($sanitizedInput, $rules);
        
        if (!empty($errors)) {
            if ($this->request->isAjax()) {
                $this->json(['success' => false, 'errors' => $errors]);
            } else {
                $config = require __DIR__ . '/../config/config.php';
                $tokenName = $config['security']['csrf_token_name'];
                
                $data = [
                    'title' => 'Login',
                    'errors' => $errors,
                    'old' => $sanitizedInput,
                    'csrf_token' => $_SESSION[$tokenName]
                ];
                
                $this->view('auth/login', $data);
            }
            return;
        }
        
        // Process login logic here
        // ...
        
        if ($this->request->isAjax()) {
            $this->json(['success' => true, 'redirect' => '/']);
        } else {
            $this->redirect('/');
        }
    }
    
    public function showRegistrationForm() {
        $config = require __DIR__ . '/../config/config.php';
        $tokenName = $config['security']['csrf_token_name'];
        
        $data = [
            'title' => 'Register',
            'csrf_token' => $_SESSION[$tokenName]
        ];
        
        $this->view('auth/register', $data);
    }
    
    public function register() {
        $input = $this->request->post();
        $sanitizedInput = $this->sanitizeInput($input);
        
        $rules = [
            'name' => 'required|min:3|max:50',
            'email' => 'required|email',
            'password' => 'required|min:6|confirmed'
        ];
        
        $errors = $this->validateInput($sanitizedInput, $rules);
        
        if (!empty($errors)) {
            if ($this->request->isAjax()) {
                $this->json(['success' => false, 'errors' => $errors]);
            } else {
                $config = require __DIR__ . '/../config/config.php';
                $tokenName = $config['security']['csrf_token_name'];
                
                $data = [
                    'title' => 'Register',
                    'errors' => $errors,
                    'old' => $sanitizedInput,
                    'csrf_token' => $_SESSION[$tokenName]
                ];
                
                $this->view('auth/register', $data);
            }
            return;
        }
        
        // Process registration logic here
        // ...
        
        if ($this->request->isAjax()) {
            $this->json(['success' => true, 'redirect' => '/login']);
        } else {
            $this->redirect('/login');
        }
    }
}
