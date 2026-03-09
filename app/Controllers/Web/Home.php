<?php

namespace App\Controllers\Web;

use App\Controllers\BaseController;

/**
 * Главная страница
 *
 * @package App\Controllers\Web
 */
class Home extends BaseController
{
    /**
     * Отображает главную страницу
     *
     * @return string
     */
    public function index(): string
    {
        return view('web/home');
    }
}