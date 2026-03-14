<?php

namespace App\Controllers\Web;

use App\Controllers\BaseController;
use App\Models\User;
use App\Models\PasswordResetModel;
use CodeIgniter\HTTP\RedirectResponse;
use Random\RandomException;
use ReflectionException;

/**
 * Веб-контроллер аутентификации
 *
 * Обрабатывает запросы на регистрацию, вход, восстановление пароля
 * и выход из системы через веб-интерфейс (не API).
 *
 * @package App\Controllers\Web
 * @noinspection PhpUnused
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
        helper('email'); // Подключаем хелпер для отправки писем
    }

    /**
     * Страница входа
     *
     * @return string|RedirectResponse
     * @noinspection PhpUnused
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
            'login' => 'required',
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

        if (!$user) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Пользователь с таким логином не найден');
        }

        if (!password_verify($password, $user['password'])) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Неверный пароль');
        }

        // Проверяем, активен ли аккаунт
        if (!$user['is_active']) {
            return redirect()->to('login')
                ->with('warning', 'Ваш email не подтверждён. Проверьте почту. Повторная регистрация будет доступна через 24 часа.');
        }

        session()->set([
            'user_id' => $user['id'],
            'username' => $user['username'],
            'email' => $user['email'],
            'is_logged_in' => true
        ]);

        return redirect()->to('dashboard')
            ->with('success', 'Добро пожаловать, ' . $user['username'] . '!');
    }

    /**
     * Страница регистрации
     *
     * @return string|RedirectResponse
     * @noinspection PhpUnused
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
        // Проверяем, не регистрировался ли этот email недавно
        $email = $this->request->getPost('email');

        // Создаём безопасный ключ для кэша (заменяем @ на _)
        $cacheKey = "register_attempt_" . str_replace('@', '_', $email);
        $lastAttempt = cache($cacheKey);

        if ($lastAttempt) {
            $timeLeft = 86400 - (time() - $lastAttempt); // 24 часа = 86400 секунд
            $hoursLeft = ceil($timeLeft / 3600);

            return redirect()->back()
                ->withInput()
                ->with('error', "Повторная регистрация с этим email будет доступна через $hoursLeft ч. Проверьте почту для активации аккаунта.");
        }

        $rules = [
            'username' => 'required|min_length[3]|is_unique[users.username]',
            'email' => 'required|valid_email|is_unique[users.email]',
            'password' => 'required|min_length[8]',
            'confirm_password' => 'required|matches[password]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $data = [
            'username' => $this->request->getPost('username'),
            'email' => $email,
            'password' => $this->request->getPost('password')
        ];

        if (!$this->userModel->save($data)) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Ошибка при создании пользователя');
        }

        // Сохраняем время регистрации в кэш на 24 часа (используем безопасный ключ)
        cache()->save($cacheKey, time(), 86400);

        // Получаем созданного пользователя с токеном
        $user = $this->userModel->where('email', $data['email'])->first();

        // Отправляем письмо с подтверждением
        sendActivationEmail($user['email'], $user['username'], $user['activation_token']);

        return redirect()->to('login')
            ->with('success', 'Регистрация успешна! Проверьте вашу почту для подтверждения email.');
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
     * @throws ReflectionException
     * @noinspection PhpUnused
     */
    public function doForgotPassword(): RedirectResponse
    {
        $rules = [
            'email' => [
                'rules'  => 'required|valid_email|is_not_unique[users.email]',
                'errors' => [
                    'is_not_unique' => 'Пользователь с таким email не найден в системе.'
                ]
            ]
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $email = $this->request->getPost('email');

        // Создаём модель для токенов
        $passwordResetModel = new PasswordResetModel();

        // Создаём токен для сброса пароля
        $token = $passwordResetModel->createToken($email);

        // Отправляем email со ссылкой для сброса пароля
        sendPasswordResetEmail($email, $token);

        log_message('info', "Password reset requested for email: $email");

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

        // Проверяем токен через модель
        $passwordResetModel = new PasswordResetModel();
        $reset = $passwordResetModel->verifyToken($token);

        if (!$reset) {
            return redirect()->to('forgot-password')
                ->with('error', 'Недействительная или истекшая ссылка для сброса пароля');
        }

        return view('auth/reset_password', ['token' => $token]);
    }

    /**
     * Обработка сброса пароля
     *
     * @param string $token Токен сброса пароля
     * @return RedirectResponse
     * @throws ReflectionException
     * @noinspection PhpUnused
     */
    public function doResetPassword(string $token): RedirectResponse
    {
        $rules = [
            'password' => 'required|min_length[8]',
            'confirm_password' => 'required|matches[password]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        // Проверяем токен
        $passwordResetModel = new PasswordResetModel();
        $reset = $passwordResetModel->verifyToken($token);

        if (!$reset) {
            return redirect()->to('forgot-password')
                ->with('error', 'Недействительная или истекшая ссылка для сброса пароля');
        }

        // Находим пользователя по email
        $user = $this->userModel->findByEmail($reset['email']);

        if (!$user) {
            log_message('error', "User not found for email: {$reset['email']} during password reset");
            return redirect()->to('login')
                ->with('error', 'Произошла ошибка. Пожалуйста, попробуйте позже.');
        }

        // Обновляем пароль
        $newPassword = $this->request->getPost('password');
        $this->userModel->updatePassword($user['id'], $newPassword);

        // Удаляем использованный токен
        $passwordResetModel->deleteToken($token);

        log_message('info', "Password reset completed for user: {$user['username']}");

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