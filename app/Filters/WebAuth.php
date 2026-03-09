<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Фильтр для защиты веб-страниц
 *
 * Проверяет, авторизован ли пользователь.
 * Если нет - перенаправляет на страницу входа.
 *
 * @package App\Filters
 */
class WebAuth implements FilterInterface
{
    /**
     * Проверка авторизации перед загрузкой страницы
     *
     * @param RequestInterface $request
     * @param array|null $arguments
     * @return RequestInterface|ResponseInterface|null
     */
    public function before(RequestInterface $request, $arguments = null): ResponseInterface|RequestInterface|null
    {
        if (!session()->get('is_logged_in')) {
            return redirect()->to('/login')
                ->with('error', 'Пожалуйста, войдите в систему для доступа к этой странице');
        }

        return $request;
    }

    /**
     * Действия после загрузки страницы
     *
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @param array|null $arguments
     * @return void
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Ничего не делаем
    }
}