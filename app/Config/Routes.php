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

        // API для контактов (JWT) - оставляем с JWT
        $routes->get('contacts', 'Api\ContactsController::index');
        $routes->get('contacts/search', 'Api\ContactsController::search');
        $routes->post('contacts/add', 'Api\ContactsController::add');
        $routes->delete('contacts/remove/(:num)', 'Api\ContactsController::remove/$1');
        $routes->get('contacts/check/(:num)', 'Api\ContactsController::check/$1');
    });

    // API для чата (без JWT, используют сессии)
    $routes->get('chat/poll', 'Api\ChatController::poll');
    $routes->post('chat/send', 'Api\ChatController::send');
    $routes->get('chat/history/(:num)', 'Api\ChatController::history/$1');
    $routes->post('chat/read/(:num)', 'Api\ChatController::markRead/$1');
    $routes->get('chat/unread-count', 'Api\ChatController::unreadCount');
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
$routes->get('/dashboard/settings', 'Web\Placeholder::index/settings', ['filter' => 'web-auth']);

// ============================================
// Профиль пользователя
// ============================================
$routes->get('/dashboard/profile', 'Web\Profile::index', ['filter' => 'web-auth']);
$routes->post('/dashboard/profile/update-name', 'Web\Profile::updateDisplayName', ['filter' => 'web-auth']);

// ============================================
// Контакты (WEB)
// ============================================
$routes->get('/contacts', 'Web\Contacts::index', ['filter' => 'web-auth']);
$routes->get('/contacts/search', 'Web\Contacts::search', ['filter' => 'web-auth']);
$routes->post('/contacts/add', 'Web\Contacts::add', ['filter' => 'web-auth']);
$routes->post('/contacts/remove/(:num)', 'Web\Contacts::remove/$1', ['filter' => 'web-auth']);
$routes->get('/contacts/get-for-chat', 'Web\Contacts::getForChat', ['filter' => 'web-auth']);

// ============================================
// Чат (WEB)
// ============================================
$routes->get('/chat', 'Web\Chat::index', ['filter' => 'web-auth']);
$routes->get('/chat/(:num)', 'Web\Chat::conversation/$1', ['filter' => 'web-auth']);

// ============================================
// Активация email
// ============================================
$routes->get('activate/(:any)', 'Web\Activation::activate/$1');

// ============================================
// Заглушки для страниц в разработке
// ============================================
$routes->get('/security', 'Web\Placeholder::security');
$routes->get('/help', 'Web\Placeholder::index/help');