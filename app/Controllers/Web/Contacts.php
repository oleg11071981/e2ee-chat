<?php

namespace App\Controllers\Web;

use App\Controllers\BaseController;
use App\Models\User;
use App\Models\Contact;
use CodeIgniter\HTTP\RedirectResponse;
use ReflectionException;

/**
 * Контроллер для работы с контактами пользователя
 *
 * @package App\Controllers\Web
 * @noinspection PhpUnused
 */
class Contacts extends BaseController
{
    protected User $userModel;
    protected Contact $contactModel;

    public function __construct()
    {
        $this->userModel = new User();
        $this->contactModel = new Contact();
    }

    /**
     * Страница со списком контактов
     *
     * @return string|RedirectResponse
     * @noinspection PhpUnused
     */
    public function index(): string|RedirectResponse
    {
        if (!session()->get('is_logged_in')) {
            return redirect()->to('login');
        }

        $userId = session()->get('user_id');
        $contacts = $this->contactModel->getContacts($userId);
        $count = $this->contactModel->getContactCount($userId);

        return view('contacts/index', [
            'contacts' => $contacts,
            'count' => $count
        ]);
    }

    /**
     * Страница поиска пользователей для добавления в контакты
     *
     * @return string|RedirectResponse
     * @noinspection PhpUnused
     */
    public function search(): string|RedirectResponse
    {
        if (!session()->get('is_logged_in')) {
            return redirect()->to('login');
        }

        $query = $this->request->getGet('q');
        $userId = session()->get('user_id');

        $users = [];
        if ($query && strlen($query) >= 2) {
            // Ищем пользователей по имени или email
            $users = $this->userModel
                ->select('id, username, email, display_name, is_active')
                ->where('id !=', $userId) // Исключаем себя
                ->groupStart()
                ->like('username', $query)
                ->orLike('email', $query)
                ->orLike('display_name', $query)
                ->groupEnd()
                ->limit(20)
                ->findAll();

            // Для каждого пользователя проверяем, есть ли уже в контактах
            foreach ($users as &$user) {
                $user['is_contact'] = $this->contactModel->isContact($userId, $user['id']);
            }
        }

        return view('contacts/search', [
            'query' => $query,
            'users' => $users
        ]);
    }

    /**
     * Добавление контакта
     *
     * @return RedirectResponse
     * @throws ReflectionException
     * @noinspection PhpUnused
     */
    public function add(): RedirectResponse
    {
        if (!session()->get('is_logged_in')) {
            return redirect()->to('login');
        }

        $contactId = (int)$this->request->getPost('contact_id');
        $userId = session()->get('user_id');

        if ($contactId <= 0) {
            return redirect()->back()
                ->with('error', 'Некорректный ID контакта');
        }

        // Проверяем, что пользователь с таким ID существует
        $contactUser = $this->userModel->find($contactId);
        if (!$contactUser) {
            return redirect()->back()
                ->with('error', 'Пользователь не найден');
        }

        if ($this->contactModel->addContact($userId, $contactId)) {
            return redirect()->to('contacts')
                ->with('success', 'Контакт успешно добавлен');
        }

        return redirect()->back()
            ->with('error', 'Не удалось добавить контакт');
    }

    /**
     * Удаление контакта
     *
     * @param int $contactId ID контакта для удаления
     * @return RedirectResponse
     * @noinspection PhpUnused
     */
    public function remove(int $contactId): RedirectResponse
    {
        if (!session()->get('is_logged_in')) {
            return redirect()->to('login');
        }

        $userId = session()->get('user_id');

        if ($this->contactModel->removeContact($userId, $contactId)) {
            return redirect()->to('contacts')
                ->with('success', 'Контакт удалён');
        }

        return redirect()->to('contacts')
            ->with('error', 'Не удалось удалить контакт');
    }
}