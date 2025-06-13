<?php
// Define application routes
$router->get('/', 'HomeController@index');
$router->get('/login', 'AuthController@showLoginForm');
$router->post('/login', 'AuthController@login');
$router->get('/register', 'AuthController@showRegistrationForm');
$router->post('/register', 'AuthController@register');
