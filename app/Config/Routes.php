<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// ============================================
// WEB маршруты (главная страница)
// ============================================
$routes->get('/', 'Web\Home::index');

// ============================================
// API маршруты
// ============================================
$routes->group('api', function($routes) {
    $routes->post('register', 'Api\AuthController::register');
    $routes->post('login', 'Api\AuthController::login');

    $routes->group('', ['filter' => 'jwt'], function($routes) {
        $routes->get('profile', 'Api\AuthController::profile');
    });
});

// ============================================
// WEB маршруты (аутентификация)
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
// WEB маршруты (личный кабинет)
// ============================================
$routes->get('/dashboard/profile', 'Web\Dashboard::profile', ['filter' => 'web-auth']);
$routes->get('/dashboard/settings', 'Web\Dashboard::settings', ['filter' => 'web-auth']);
$routes->post('/dashboard/profile/update', 'Web\Dashboard::updateProfile', ['filter' => 'web-auth']);

// ============================================
// Активация email
// ============================================
$routes->get('activate/(:any)', 'Web\Activation::activate/$1');

// ============================================
// Повторная отправка письма активации (УДАЛЕНА)
// ============================================
// $routes->post('resend-activation', 'Web\Activation::resend');