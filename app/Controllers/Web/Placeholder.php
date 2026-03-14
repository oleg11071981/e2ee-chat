<?php

namespace App\Controllers\Web;

use App\Controllers\BaseController;

/**
 * Контроллер для страниц-заглушек
 *
 * Показывает страницу "В разработке" для нереализованного функционала
 *
 * @package App\Controllers\Web
 * @noinspection PhpUnused
 */
class Placeholder extends BaseController
{
    /**
     * Страница "В разработке"
     *
     * @param string $page Название страницы (из URL)
     * @return string
     * @noinspection PhpUnused
     */
    public function index(string $page = ''): string
    {
        // Если название страницы не передано, используем общее
        if (empty($page)) {
            $page = 'Страница';
        }

        // Форматируем название (заменяем дефисы на пробелы)
        $pageName = str_replace('-', ' ', $page);

        return view('placeholder/index', [
            'pageName' => $pageName,
            'pageUrl' => $page
        ]);
    }

    /**
     * Заглушка для чата
     *
     * @return string
     * @noinspection PhpUnused
     */
    public function chat(): string
    {
        return view('placeholder/index', [
            'pageName' => 'Чат',
            'pageUrl' => 'chat',
            'icon' => 'fa-comments'
        ]);
    }

    /**
     * Заглушка для контактов
     *
     * @return string
     * @noinspection PhpUnused
     */
    public function contacts(): string
    {
        return view('placeholder/index', [
            'pageName' => 'Контакты',
            'pageUrl' => 'contacts',
            'icon' => 'fa-address-book'
        ]);
    }

    /**
     * Заглушка для безопасности
     *
     * @return string
     * @noinspection PhpUnused
     */
    public function security(): string
    {
        return view('placeholder/index', [
            'pageName' => 'Безопасность',
            'pageUrl' => 'security',
            'icon' => 'fa-shield-alt'
        ]);
    }

    /**
     * Заглушка для настроек
     *
     * @return string
     * @noinspection PhpUnused
     */
    public function settings(): string
    {
        return view('placeholder/index', [
            'pageName' => 'Настройки',
            'pageUrl' => 'settings',
            'icon' => 'fa-cog'
        ]);
    }
}