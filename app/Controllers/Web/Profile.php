<?php

namespace App\Controllers\Web;

use App\Controllers\BaseController;
use App\Models\User;
use CodeIgniter\HTTP\RedirectResponse;
use ReflectionException;

/**
 * Контроллер профиля пользователя
 *
 * @package App\Controllers\Web
 * @noinspection PhpUnused
 */
class Profile extends BaseController
{
    protected User $userModel;

    public function __construct()
    {
        $this->userModel = new User();
    }

    /**
     * Страница настроек профиля
     */
    public function index(): string|RedirectResponse
    {
        if (!session()->get('is_logged_in')) {
            return redirect()->to('login');
        }

        $userId = session()->get('user_id');
        $user = $this->userModel->select('id, username, display_name, email')->find($userId);

        return view('profile/index', ['user' => $user]);
    }

    /**
     * Обновление отображаемого имени
     * @noinspection PhpUnused
     * @throws ReflectionException
     */
    /**
     * Обновление отображаемого имени
     * @noinspection PhpUnused
     * @throws ReflectionException
     */
    public function updateDisplayName(): RedirectResponse
    {
        if (!session()->get('is_logged_in')) {
            return redirect()->to('login');
        }

        $rules = [
            'display_name' => 'required|min_length[2]|max_length[100]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $userId = session()->get('user_id');
        $displayName = $this->request->getPost('display_name');

        $this->userModel->update($userId, [
            'display_name' => $displayName
        ]);

        // Исправлено: редирект на /dashboard/profile, а не просто /profile
        return redirect()->to('dashboard/profile')
            ->with('success', 'Имя успешно обновлено');
    }
}