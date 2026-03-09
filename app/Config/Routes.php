<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Web\Home::index');

$routes->group('api', function($routes) {
    $routes->post('register', 'Api\AuthController::register');
    $routes->post('login', 'Api\AuthController::login');

    $routes->group('', ['filter' => 'jwt'], function($routes) {
        $routes->get('profile', 'Api\AuthController::profile');
    });
});

// ============================================
// WEB маршруты
// ============================================
$routes->get('/login', 'Web\Auth::login');
$routes->post('/login', 'Web\Auth::doLogin');
$routes->get('/register', 'Web\Auth::register');
$routes->post('/register', 'Web\Auth::doRegister');
$routes->get('/forgot-password', 'Web\Auth::forgotPassword');
$routes->post('/forgot-password', 'Web\Auth::doForgotPassword');
$routes->get('/reset-password/(:any)', 'Web\Auth::resetPassword/$1');
$routes->post('/reset-password/(:any)', 'Web\Auth::doResetPassword/$1');
$routes->get('/dashboard', 'Web\Dashboard::index', ['filter' => 'web-auth']);
$routes->get('/logout', 'Web\Auth::logout');

// ============================================
// WEB маршруты (восстановление пароля)
// ============================================
$routes->get('/forgot-password', 'Web\Auth::forgotPassword');
$routes->post('/forgot-password', 'Web\Auth::doForgotPassword');
$routes->get('/reset-password/(:any)', 'Web\Auth::resetPassword/$1');
$routes->post('/reset-password/(:any)', 'Web\Auth::doResetPassword/$1');

// ============================================
// WEB маршруты (личный кабинет)
// ============================================
$routes->get('/dashboard', 'Web\Dashboard::index', ['filter' => 'web-auth']);
$routes->get('/dashboard/profile', 'Web\Dashboard::profile', ['filter' => 'web-auth']);
$routes->get('/dashboard/settings', 'Web\Dashboard::settings', ['filter' => 'web-auth']);
$routes->post('/dashboard/profile/update', 'Web\Dashboard::updateProfile', ['filter' => 'web-auth']);