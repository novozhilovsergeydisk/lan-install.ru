<?php

class AuthMiddleware {
    public function handle($request, $next) {
        $auth = new Auth();
        
        if (!$auth->check()) {
            // Store the intended URL for redirecting after login
            $_SESSION['redirect_after_login'] = $request->getPath();
            
            // Set flash message
            $_SESSION['flash_message'] = 'Пожалуйста, войдите в систему для доступа к этой странице';
            $_SESSION['flash_type'] = 'warning';
            
            // Redirect to login page
            header('Location: /login');
            exit();
        }
        
        return $next($request);
    }
}
