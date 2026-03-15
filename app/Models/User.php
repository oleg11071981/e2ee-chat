<?php

namespace App\Models;

use CodeIgniter\Model;
use Random\RandomException;
use ReflectionException;

/**
 * Модель пользователя
 *
 * Отвечает за работу с таблицей users в базе данных.
 * Содержит правила валидации, методы хеширования пароля и настройки модели.
 *
 * @package App\Models
 * @noinspection PhpUnused
 */
class User extends Model
{
    /**
     * Имя таблицы в базе данных
     * @var string
     */
    protected $table = 'users';

    /**
     * Первичный ключ таблицы
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * Автоинкремент первичного ключа
     * @var bool
     */
    protected $useAutoIncrement = true;

    /**
     * Тип возвращаемых данных
     * @var string 'array' - возвращает массивы, 'object' - объекты
     */
    protected $returnType = 'array';

    /**
     * Использование "мягкого удаления"
     * @var bool false - записи удаляются физически
     */
    protected $useSoftDeletes = false;

    /**
     * Поля, разрешённые для массового заполнения
     * @var array
     */
    protected $allowedFields = [
        'username',          // Имя пользователя (уникальное)
        'display_name',      // Имя пользователя в чатах
        'email',             // Email (уникальный)
        'password',          // Хеш пароля
        'is_active',         // Статус активации (0/1)
        'activation_token',  // Токен для подтверждения email
        'activated_at'       // Дата активации аккаунта
    ];

    /**
     * Использование временных меток
     * @var bool true - автоматическое заполнение created_at и updated_at
     */
    protected $useTimestamps = true;

    /**
     * Формат даты и времени
     * @var string
     */
    protected $dateFormat = 'datetime';

    /**
     * Поле с датой создания записи
     * @var string
     */
    protected $createdField = 'created_at';

    /**
     * Поле с датой обновления записи
     * @var string
     */
    protected $updatedField = 'updated_at';

    /**
     * Правила валидации для полей
     * @var array
     */
    protected $validationRules = [
        'username' => 'required|min_length[3]|max_length[100]|is_unique[users.username]',
        'email'    => 'required|valid_email|is_unique[users.email]',
        'password' => 'required|min_length[8]'
    ];

    /**
     * Пользовательские сообщения об ошибках валидации
     * @var array
     */
    protected $validationMessages = [
        'username' => [
            'is_unique' => 'Это имя пользователя уже занято'
        ],
        'email' => [
            'is_unique' => 'Этот email уже зарегистрирован'
        ]
    ];

    /**
     * События, выполняемые перед вставкой записи
     * @var array
     */
    protected $beforeInsert = ['hashPassword', 'generateActivationToken'];

    /**
     * События, выполняемые перед обновлением записи
     * @var array
     */
    protected $beforeUpdate = ['hashPassword'];

    /**
     * Хеширование пароля перед сохранением в базу данных
     *
     * Этот метод автоматически вызывается при вставке и обновлении записи.
     * Использует PASSWORD_DEFAULT (bcrypt) для хеширования.
     * Включает защиту от двойного хеширования.
     *
     * @used-by self::$beforeInsert
     * @used-by self::$beforeUpdate
     * @param array $data Массив данных для вставки/обновления
     * @return array Модифицированный массив с хешированным паролем
     * @noinspection PhpUnused
     */
    protected function hashPassword(array $data): array
    {
        // Данные могут быть либо в $data['data'], либо прямо в $data
        $passwordField = null;

        if (isset($data['data']['password'])) {
            $passwordField = &$data['data']['password'];
        } elseif (isset($data['password'])) {
            $passwordField = &$data['password'];
        }

        if ($passwordField !== null) {
            $info = password_get_info($passwordField);
            if ($info['algo'] === 0) {
                $passwordField = password_hash($passwordField, PASSWORD_DEFAULT);
            }
        }

        return $data;
    }

    /**
     * Генерация токена активации для нового пользователя
     *
     * Создаёт уникальный токен для ссылки подтверждения email.
     * Автоматически вызывается только при создании новой записи.
     *
     * @used-by self::$beforeInsert
     * @param array $data Массив данных для вставки
     * @return array Модифицированный массив с добавленным токеном
     * @throws RandomException
     * @noinspection PhpUnused
     */
    protected function generateActivationToken(array $data): array
    {
        if (!isset($data['data']['activation_token'])) {
            $data['data']['activation_token'] = bin2hex(random_bytes(32));
            $data['data']['is_active'] = 0;
        }
        return $data;
    }

    /**
     * Активация пользователя по токену
     *
     * @param string $token Токен активации из ссылки
     * @return bool true если активация успешна, false если токен не найден
     * @noinspection PhpUnused
     * @throws ReflectionException
     */
    public function activate(string $token): bool
    {
        $user = $this->where('activation_token', $token)
            ->where('is_active', 0)
            ->first();

        if (!$user) {
            return false;
        }

        return $this->update($user['id'], [
            'is_active' => 1,
            'activation_token' => null,
            'activated_at' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Получение пользователя по email
     *
     * @param string $email Email пользователя
     * @return array|null Массив с данными пользователя или null
     * @noinspection PhpUnused
     */
    public function findByEmail(string $email): ?array
    {
        return $this->where('email', $email)->first();
    }

    /**
     * Получение пользователя по username
     *
     * @param string $username Имя пользователя
     * @return array|null Массив с данными пользователя или null
     * @noinspection PhpUnused
     */
    public function findByUsername(string $username): ?array
    {
        return $this->where('username', $username)->first();
    }

    /**
     * Проверка, активен ли пользователь
     *
     * @param int $userId ID пользователя
     * @return bool true если активен, false если нет
     * @noinspection PhpUnused
     */
    public function isActive(int $userId): bool
    {
        $user = $this->select('is_active')->find($userId);
        return $user && $user['is_active'];
    }

    /**
     * Обновление пароля пользователя
     *
     * @param int $userId ID пользователя
     * @param string $newPassword Новый пароль
     * @return bool Результат операции
     * @noinspection PhpUnused
     * @throws ReflectionException
     */
    public function updatePassword(int $userId, string $newPassword): bool
    {
        return $this->update($userId, [
            'password' => password_hash($newPassword, PASSWORD_DEFAULT)
        ]);
    }
}