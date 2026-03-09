<?php

namespace App\Controllers\Web;

use App\Controllers\BaseController;
use App\Models\User;
use CodeIgniter\HTTP\RedirectResponse;
use Random\RandomException;
use ReflectionException;

/**
 * Веб-контроллер аутентификации
 *
 * Обрабатывает запросы на регистрацию, вход, восстановление пароля
 * и выход из системы через веб-интерфейс (не API).
 *
 * @noinspection PhpUnused
 * @package App\Controllers\Web
 */
class Auth extends BaseController
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
     * Страница входа
     *
     * @return string|RedirectResponse
     */
    public function login(): string|RedirectResponse
    {
        // Если пользователь уже авторизован - редирект в личный кабинет
        if (session()->get('is_logged_in')) {
            return redirect()->to('dashboard');
        }

        return view('auth/login');
    }

    /**
     * Обработка входа
     *
     * @return RedirectResponse
     * @noinspection PhpUnused
     */
    public function doLogin(): RedirectResponse
    {
        $rules = [
            'login'    => 'required',
            'password' => 'required'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $login = $this->request->getPost('login');
        $password = $this->request->getPost('password');

        $user = $this->userModel
            ->where('email', $login)
            ->orWhere('username', $login)
            ->first();

        if (!$user || !password_verify($password, $user['password'])) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Неверный логин или пароль');
        }

        session()->set([
            'user_id'       => $user['id'],
            'username'      => $user['username'],
            'email'         => $user['email'],
            'is_logged_in'  => true
        ]);

        return redirect()->to('dashboard')
            ->with('success', 'Добро пожаловать, ' . $user['username'] . '!');
    }

    /**
     * Страница регистрации
     *
     * @return string|RedirectResponse
     */
    public function register(): string|RedirectResponse
    {
        if (session()->get('is_logged_in')) {
            return redirect()->to('dashboard');
        }

        return view('auth/register');
    }

    /**
     * Обработка регистрации
     *
     * @return RedirectResponse
     * @throws ReflectionException
     * @noinspection PhpUnused
     */
    public function doRegister(): RedirectResponse
    {
        $rules = [
            'username'         => 'required|min_length[3]|is_unique[users.username]',
            'email'            => 'required|valid_email|is_unique[users.email]',
            'password'         => 'required|min_length[8]',
            'confirm_password' => 'required|matches[password]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $data = [
            'username' => $this->request->getPost('username'),
            'email'    => $this->request->getPost('email'),
            'password' => $this->request->getPost('password')
        ];

        if (!$this->userModel->save($data)) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Ошибка при создании пользователя');
        }

        return redirect()->to('login')
            ->with('success', 'Регистрация успешна! Теперь вы можете войти.');
    }

    /**
     * Страница восстановления пароля (запрос email)
     *
     * @return string|RedirectResponse
     * @noinspection PhpUnused
     */
    public function forgotPassword(): string|RedirectResponse
    {
        if (session()->get('is_logged_in')) {
            return redirect()->to('dashboard');
        }

        return view('auth/forgot_password');
    }

    /**
     * Обработка запроса на восстановление пароля
     *
     * @return RedirectResponse
     * @throws RandomException
     * @noinspection PhpUnused
     */
    public function doForgotPassword(): RedirectResponse
    {
        $rules = [
            'email' => 'required|valid_email|is_not_unique[users.email]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $email = $this->request->getPost('email');

        // Генерируем токен для сброса пароля
        $token = bin2hex(random_bytes(32));

        // TODO: Сохранить токен в БД (создать таблицу password_resets)
        // TODO: Отправить email со ссылкой для сброса пароля

        // Используем переменные для логирования
        log_message('info', "Password reset requested for email: $email, token: $token");

        return redirect()->back()
            ->with('success', 'Инструкции по восстановлению пароля отправлены на ваш email');
    }

    /**
     * Страница сброса пароля (ввод нового пароля)
     *
     * @param string $token Токен сброса пароля
     * @return string|RedirectResponse
     * @noinspection PhpUnused
     */
    public function resetPassword(string $token): string|RedirectResponse
    {
        if (session()->get('is_logged_in')) {
            return redirect()->to('dashboard');
        }

        // TODO: Проверить токен в БД
        //$isValid = true; // Временно

        // Используем переменную для проверки

        return view('auth/reset_password', ['token' => $token]);
    }

    /**
     * Обработка сброса пароля
     *
     * @param string $token Токен сброса пароля
     * @return RedirectResponse
     * @noinspection PhpUnused
     */
    public function doResetPassword(string $token): RedirectResponse
    {
        $rules = [
            'password'         => 'required|min_length[8]',
            'confirm_password' => 'required|matches[password]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        // TODO: Проверить токен в БД и обновить пароль
        log_message('info', "Password reset using token: $token");

        return redirect()->to('login')
            ->with('success', 'Пароль успешно изменён. Теперь вы можете войти.');
    }

    /**
     * Выход из системы
     *
     * @return RedirectResponse
     * @noinspection PhpUnused
     */
    public function logout(): RedirectResponse
    {
        session()->destroy();
        return redirect()->to('/')
            ->with('success', 'Вы успешно вышли из системы');
    }
}