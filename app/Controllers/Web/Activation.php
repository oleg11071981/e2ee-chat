<?php

namespace App\Controllers\Web;

use App\Controllers\BaseController;
use App\Models\User;
use CodeIgniter\HTTP\RedirectResponse;
use Random\RandomException;
use ReflectionException;

/**
 * Контроллер активации email
 *
 * Обрабатывает подтверждение email по ссылке из письма.
 *
 * @package App\Controllers\Web
 * @noinspection PhpUnused
 */
class Activation extends BaseController
{
    /**
     * Модель пользователя
     *
     * @var User
     */
    protected User $userModel;

    /**
     * Конструктор контроллера
     */
    public function __construct()
    {
        $this->userModel = new User();
    }

    /**
     * Активация аккаунта по токену
     *
     * @param string $token Токен активации из ссылки
     * @return RedirectResponse
     *
     * @noinspection PhpUnused
     * @throws ReflectionException
     */
    public function activate(string $token): RedirectResponse
    {
        // Проверяем формат токена (должен быть 64 символа)
        if (empty($token) || strlen($token) !== 64) {
            return redirect()->to('login')
                ->with('error', 'Недействительная ссылка активации');
        }

        if ($this->userModel->activate($token)) {
            return redirect()->to('login')
                ->with('success', 'Ваш email успешно подтверждён! Теперь вы можете войти.');
        }

        return redirect()->to('login')
            ->with('error', 'Недействительная или истекшая ссылка активации');
    }

    /**
     * Повторная отправка письма активации
     *
     * @return RedirectResponse
     * @noinspection PhpUnused
     * @throws RandomException|ReflectionException
     */
    public function resend(): RedirectResponse
    {
        $email = session()->getFlashdata('email');

        if (!$email) {
            return redirect()->to('login')
                ->with('error', 'Не удалось определить email для повторной отправки');
        }

        $user = $this->userModel->where('email', $email)->first();

        if (!$user) {
            return redirect()->to('login')
                ->with('error', 'Пользователь не найден');
        }

        if ($user['is_active']) {
            return redirect()->to('login')
                ->with('success', 'Ваш аккаунт уже активирован');
        }

        // Генерируем новый токен
        $newToken = bin2hex(random_bytes(32));
        $this->userModel->update($user['id'], [
            'activation_token' => $newToken
        ]);

        // Отправляем новое письмо (функция будет добавлена позже)
        // sendActivationEmail($user['email'], $user['username'], $newToken);

        return redirect()->to('login')
            ->with('success', 'Новое письмо с подтверждением отправлено на ваш email');
    }
}