<?php

namespace App\Controllers\Web;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\RedirectResponse;

/**
 * Главная страница
 *
 * @package App\Controllers\Web
 */
class Home extends BaseController
{
    /**
     * Отображает главную страницу
     * Если пользователь авторизован - редирект в личный кабинет
     *
     * @return string|RedirectResponse
     */
    public function index(): string|RedirectResponse
    {
        // Если пользователь авторизован - сразу в личный кабинет
        if (session()->get('is_logged_in')) {
            return redirect()->to('dashboard');
        }

        // Передаём переменную в представление
        return view('web/home', ['hide_nav' => true]);
    }
}