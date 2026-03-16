<?php

namespace App\Controllers\Web;

use App\Controllers\BaseController;
use App\Models\User;
use App\Models\Contact;
use CodeIgniter\HTTP\RedirectResponse;
use ReflectionException;

/**
 * Контроллер личного кабинета
 *
 * @package App\Controllers\Web
 * @noinspection PhpUnused
 */
class Dashboard extends BaseController
{
    protected User $userModel;
    protected Contact $contactModel;

    public function __construct()
    {
        $this->userModel = new User();
        $this->contactModel = new Contact();
    }

    /**
     * Главная страница личного кабинета
     *
     * @return string|RedirectResponse
     * @noinspection PhpUnused
     */
    public function index(): string|RedirectResponse
    {
        if (!session()->get('is_logged_in')) {
            return redirect()->to('login')
                ->with('error', 'Пожалуйста, войдите в систему');
        }

        $userId = session()->get('user_id');
        $user = $this->userModel->select('id, username, email, created_at')->find($userId);

        if (!$user) {
            session()->destroy();
            return redirect()->to('login')
                ->with('error', 'Пользователь не найден');
        }

        // Получаем количество контактов
        $contactCount = $this->contactModel->getContactCount($userId);

        $data = [
            'user' => $user,
            'contactCount' => $contactCount
        ];

        return view('dashboard/index', $data);
    }

    /**
     * Страница профиля пользователя
     *
     * @return string|RedirectResponse
     * @noinspection PhpUnused
     */
    public function profile(): string|RedirectResponse
    {
        if (!session()->get('is_logged_in')) {
            return redirect()->to('login');
        }

        $userId = session()->get('user_id');
        $user = $this->userModel->select('id, username, email, display_name, created_at')->find($userId);

        if (!$user) {
            return redirect()->to('dashboard')
                ->with('error', 'Пользователь не найден');
        }

        return view('dashboard/profile', ['user' => $user]);
    }

    /**
     * Настройки пользователя
     *
     * @return string|RedirectResponse
     * @noinspection PhpUnused
     */
    public function settings(): string|RedirectResponse
    {
        if (!session()->get('is_logged_in')) {
            return redirect()->to('login');
        }

        return view('dashboard/settings');
    }

    /**
     * Обновление профиля
     *
     * @return RedirectResponse
     * @throws ReflectionException
     * @noinspection PhpUnused
     */
    public function updateProfile(): RedirectResponse
    {
        if (!session()->get('is_logged_in')) {
            return redirect()->to('login');
        }

        $userId = session()->get('user_id');

        $rules = [
            'username' => "permit_empty|min_length[3]|is_unique[users.username,id,$userId]",
            'email'    => "permit_empty|valid_email|is_unique[users.email,id,$userId]"
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $data = [];
        if ($this->request->getPost('username')) {
            $data['username'] = $this->request->getPost('username');
        }
        if ($this->request->getPost('email')) {
            $data['email'] = $this->request->getPost('email');
        }

        if (!empty($data)) {
            $this->userModel->update($userId, $data);
            session()->set('username', $data['username'] ?? session()->get('username'));
            session()->set('email', $data['email'] ?? session()->get('email'));
        }

        return redirect()->to('/dashboard/profile')
            ->with('success', 'Профиль успешно обновлён');
    }
}