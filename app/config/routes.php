<?php
// Define application routes

// Public routes
$router->get('/login', 'AuthController@showLoginForm');
$router->post('/login', 'AuthController@login');
$router->get('/register', 'AuthController@showRegistrationForm');
$router->post('/register', 'AuthController@register');
$router->get('/forgot-password', 'AuthController@showForgotPasswordForm');
$router->post('/forgot-password', 'AuthController@sendResetLink');
$router->get('/reset-password/{token}', 'AuthController@showResetPasswordForm');
$router->post('/reset-password', 'AuthController@resetPassword');

// Protected routes (require authentication)
$router->group(['middleware' => ['auth']], function($router) {
    $router->get('/', 'HomeController@index');
    $router->get('/home', 'HomeController@index');
    $router->post('/logout', 'AuthController@logout');
    
    // Profile routes
    $router->get('/profile', 'ProfileController@show');
    $router->put('/profile', 'ProfileController@update');
    $router->get('/profile/password', 'ProfileController@showPasswordForm');
    $router->put('/profile/password', 'ProfileController@updatePassword');
});
