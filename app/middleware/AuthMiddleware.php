<?php
class AuthMiddleware {
    public function handle($request, $next) {
        $auth = new Auth();
        
        // Check if user is authenticated
        if (!$auth->check()) {
            // Store the intended URL for redirect after login
            $_SESSION['redirect_after_login'] = $request->getPath();
            
            // Redirect to login page
            header('Location: /login');
            exit();
        }
        
        // User is authenticated, proceed to the next middleware/controller
        return $next($request);
    }
}
