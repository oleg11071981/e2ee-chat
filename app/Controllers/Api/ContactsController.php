<?php

namespace App\Controllers\Api;

use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\RESTful\ResourceController;
use App\Models\Contact;
use App\Models\User;
use ReflectionException;

/**
 * API для работы с контактами (AJAX)
 *
 * @package App\Controllers\Api
 * @noinspection PhpUnused
 */
class ContactsController extends ResourceController
{
    protected $format = 'json';

    protected Contact $contactModel;
    protected User $userModel;

    public function __construct()
    {
        $this->contactModel = new Contact();
        $this->userModel = new User();
    }

    /**
     * Получение списка контактов
     * GET /api/contacts
     */
    public function index(): ResponseInterface
    {
        $userId = $this->request->user->user_id ?? null;

        if (!$userId) {
            return $this->failUnauthorized('Требуется авторизация');
        }

        $contacts = $this->contactModel->getContacts($userId);
        $count = $this->contactModel->getContactCount($userId);

        return $this->respond([
            'status' => 'success',
            'data' => [
                'contacts' => $contacts,
                'count' => $count
            ]
        ]);
    }

    /**
     * Поиск пользователей для добавления в контакты
     * GET /api/contacts/search
     */
    public function search(): ResponseInterface
    {
        $userId = $this->request->user->user_id ?? null;

        if (!$userId) {
            return $this->failUnauthorized('Требуется авторизация');
        }

        $query = $this->request->getGet('q');

        if (!$query || strlen($query) < 2) {
            return $this->respond([
                'status' => 'success',
                'data' => []
            ]);
        }

        // Ищем пользователей по имени или email
        $users = $this->userModel
            ->select('id, username, email, display_name, is_active')
            ->where('id !=', $userId)
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

        return $this->respond([
            'status' => 'success',
            'data' => $users
        ]);
    }

    /**
     * Добавление контакта
     * POST /api/contacts/add
     */
    public function add(): ResponseInterface
    {
        $userId = $this->request->user->user_id ?? null;

        if (!$userId) {
            return $this->failUnauthorized('Требуется авторизация');
        }

        $contactId = (int)$this->request->getPost('contact_id');

        if ($contactId <= 0) {
            return $this->failValidationErrors('Некорректный ID контакта');
        }

        // Проверяем существование пользователя
        $contactUser = $this->userModel->find($contactId);
        if (!$contactUser) {
            return $this->failNotFound('Пользователь не найден');
        }

        // Проверяем, не является ли контакт уже добавленным
        if ($this->contactModel->isContact($userId, $contactId)) {
            return $this->fail('Этот пользователь уже в ваших контактах', 409);
        }

        try {
            if ($this->contactModel->addContact($userId, $contactId)) {
                return $this->respond([
                    'status' => 'success',
                    'message' => 'Контакт успешно добавлен'
                ]);
            }

            return $this->failServerError('Не удалось добавить контакт');

        } catch (ReflectionException) {
            return $this->failServerError('Ошибка базы данных');
        }
    }

    /**
     * Удаление контакта
     * DELETE /api/contacts/remove/{contactId}
     */
    public function remove($contactId = null): ResponseInterface
    {
        $userId = $this->request->user->user_id ?? null;

        if (!$userId) {
            return $this->failUnauthorized('Требуется авторизация');
        }

        $contactId = (int)$contactId;

        if ($contactId <= 0) {
            return $this->failValidationErrors('Некорректный ID контакта');
        }

        if ($this->contactModel->removeContact($userId, $contactId)) {
            return $this->respond([
                'status' => 'success',
                'message' => 'Контакт успешно удалён'
            ]);
        }

        return $this->failNotFound('Контакт не найден');
    }

    /**
     * Проверка статуса контакта
     * GET /api/contacts/check/{contactId}
     */
    public function check($contactId = null): ResponseInterface
    {
        $userId = $this->request->user->user_id ?? null;

        if (!$userId) {
            return $this->failUnauthorized('Требуется авторизация');
        }

        $contactId = (int)$contactId;

        if ($contactId <= 0) {
            return $this->failValidationErrors('Некорректный ID контакта');
        }

        $isContact = $this->contactModel->isContact($userId, $contactId);

        return $this->respond([
            'status' => 'success',
            'data' => [
                'is_contact' => $isContact
            ]
        ]);
    }
}