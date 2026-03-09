<?php

namespace App\Controllers\Api;

use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\RESTful\ResourceController;
use App\Models\User;
use App\Libraries\JwtHandler;

/**
 * Контроллер аутентификации
 *
 * Обрабатывает запросы на регистрацию, вход в систему и получение профиля пользователя.
 * Использует JWT для аутентификации и ResourceController для REST API.
 *
 * @package App\Controllers\Api
 * @noinspection PhpUnused
 * @see https://codeigniter.com/user_guide/incoming/restful.html Документация ResourceController
 */
class AuthController extends ResourceController
{
    /**
     * Имя модели для работы с пользователями
     *
     * @var string
     */
    protected $modelName = User::class;

    /**
     * Формат ответа API
     *
     * @var string
     */
    protected $format = 'json';

    /**
     * Обработчик JWT
     *
     * @var JwtHandler
     */
    protected JwtHandler $jwt;

    /**
     * Конструктор контроллера
     *
     * Инициализирует обработчик JWT для использования в методах.
     */
    public function __construct()
    {
        $this->jwt = new JwtHandler();
    }

    /**
     * Регистрация нового пользователя
     *
     * POST /api/register
     *
     * @return ResponseInterface JSON ответ с результатом регистрации
     *
     * Тело запроса (JSON):
     * {
     *   "username": "john_doe",
     *   "email": "john@example.com",
     *   "password": "secret123"
     * }
     *
     * Успешный ответ (201 Created):
     * {
     *   "status": "success",
     *   "message": "Пользователь успешно создан",
     *   "data": {
     *     "user_id": 1
     *   }
     * }
     *
     * Ошибки:
     * - 400 Bad Request - ошибки валидации
     * - 500 Internal Server Error - ошибка при сохранении
     */
    public function register(): ResponseInterface
    {
        $rules = [
            'username' => 'required|min_length[3]|is_unique[users.username]',
            'email'    => 'required|valid_email|is_unique[users.email]',
            'password' => 'required|min_length[8]'
        ];

        if (!$this->validate($rules)) {
            return $this->failValidationErrors($this->validator->getErrors());
        }

        $data = $this->request->getJSON(true);

        if (!$this->model->save($data)) {
            return $this->failServerError('Ошибка при создании пользователя');
        }

        $userId = $this->model->getInsertID();

        return $this->respondCreated([
            'status'  => 'success',
            'message' => 'Пользователь успешно создан',
            'data'    => [
                'user_id' => $userId
            ]
        ]);
    }

    /**
     * Вход в систему
     *
     * POST /api/login
     *
     * @return ResponseInterface JSON ответ с JWT токеном
     *
     * Тело запроса (JSON):
     * {
     *   "login": "john_doe",    // username или email
     *   "password": "secret123"
     * }
     *
     * Успешный ответ (200 OK):
     * {
     *   "status": "success",
     *   "data": {
     *     "access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
     *     "token_type": "Bearer",
     *     "expires_in": 604800,
     *     "user": {
     *       "id": 1,
     *       "username": "john_doe",
     *       "email": "john@example.com"
     *     }
     *   }
     * }
     * @noinspection PhpUnused
     * Ошибки:
     * - 400 Bad Request - ошибки валидации
     * - 401 Unauthorized - неверный логин или пароль
     */
    public function login(): ResponseInterface
    {
        $rules = [
            'login'    => 'required',
            'password' => 'required'
        ];

        if (!$this->validate($rules)) {
            return $this->failValidationErrors($this->validator->getErrors());
        }

        $data = $this->request->getJSON(true);

        $login = $data['login'];
        $password = $data['password'];

        // Ищем пользователя по email или username
        $user = $this->model
            ->where('email', $login)
            ->orWhere('username', $login)
            ->first();

        if (!$user || !password_verify($password, $user['password'])) {
            return $this->failUnauthorized('Неверный логин или пароль');
        }

        // Генерируем JWT токен
        $token = $this->jwt->generateToken([
            'user_id'  => $user['id'],
            'username' => $user['username'],
            'email'    => $user['email']
        ]);

        return $this->respond([
            'status' => 'success',
            'data'   => [
                'access_token' => $token,
                'token_type'   => 'Bearer',
                'expires_in'   => 604800, // 7 дней в секундах
                'user'         => [
                    'id'       => $user['id'],
                    'username' => $user['username'],
                    'email'    => $user['email']
                ]
            ]
        ]);
    }

    /**
     * Получение профиля текущего пользователя
     *
     * GET /api/profile
     *
     * @return ResponseInterface JSON ответ с данными пользователя
     *
     * Заголовки запроса:
     * Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...
     *
     * Успешный ответ (200 OK):
     * {
     *   "status": "success",
     *   "data": {
     *     "id": 1,
     *     "username": "john_doe",
     *     "email": "john@example.com",
     *     "created_at": "2026-03-01 12:00:00"
     *   }
     * }
     * @noinspection PhpUnused
     * Ошибки:
     * - 401 Unauthorized - пользователь не идентифицирован (нет токена)
     * - 404 Not Found - пользователь не найден в БД
     *
     * @see JWTAuth Фильтр, который добавляет заголовок X-User-Id
     */
    public function profile(): ResponseInterface
    {
        // Получаем ID пользователя из заголовка (устанавливается в фильтре JWTAuth)
        $userId = $this->request->getHeaderLine('X-User-Id');

        if (!$userId) {
            return $this->failUnauthorized('Пользователь не идентифицирован');
        }

        $user = $this->model->select('id, username, email, created_at')->find($userId);

        if (!$user) {
            return $this->failNotFound('Пользователь не найден');
        }

        return $this->respond([
            'status' => 'success',
            'data'   => $user
        ]);
    }
}