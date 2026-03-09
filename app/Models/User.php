<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Модель пользователя
 *
 * Отвечает за работу с таблицей users в базе данных.
 * Содержит правила валидации, методы хеширования пароля и настройки модели.
 *
 * @package App\Models
 * @see https://codeigniter.com/user_guide/models/model.html Документация CI4 Models
 */
class User extends Model
{
    /**
     * Имя таблицы в базе данных
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * Первичный ключ таблицы
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * Автоинкремент первичного ключа
     *
     * @var bool
     */
    protected $useAutoIncrement = true;

    /**
     * Тип возвращаемых данных
     *
     * @var string 'array' - возвращает массивы, 'object' - объекты
     */
    protected $returnType = 'array';

    /**
     * Использование "мягкого удаления"
     *
     * @var bool false - записи удаляются физически
     */
    protected $useSoftDeletes = false;

    /**
     * Поля, разрешённые для массового заполнения
     *
     * @var array
     */
    protected $allowedFields = [
        'username',  // Имя пользователя (уникальное)
        'email',     // Email (уникальный)
        'password'   // Хеш пароля
    ];

    /**
     * Использование временных меток
     *
     * @var bool true - автоматическое заполнение created_at и updated_at
     */
    protected $useTimestamps = true;

    /**
     * Формат даты и времени
     *
     * @var string
     */
    protected $dateFormat = 'datetime';

    /**
     * Поле с датой создания записи
     *
     * @var string
     */
    protected $createdField = 'created_at';

    /**
     * Поле с датой обновления записи
     *
     * @var string
     */
    protected $updatedField = 'updated_at';

    /**
     * Правила валидации для полей
     *
     * @var array
     *
     * Правила:
     * - username: обязательно, мин. 3 символа, макс. 100, уникальное
     * - email: обязательно, валидный email, уникальный
     * - password: обязательно, мин. 8 символов
     */
    protected $validationRules = [
        'username' => 'required|min_length[3]|max_length[100]|is_unique[users.username]',
        'email'    => 'required|valid_email|is_unique[users.email]',
        'password' => 'required|min_length[8]'
    ];

    /**
     * Пользовательские сообщения об ошибках валидации
     *
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
     *
     * @var array
     */
    protected $beforeInsert = ['hashPassword'];

    /**
     * События, выполняемые перед обновлением записи
     *
     * @var array
     */
    protected $beforeUpdate = ['hashPassword'];

    /**
     * Хеширование пароля перед сохранением в базу данных
     *
     * Этот метод автоматически вызывается при вставке и обновлении записи.
     * Использует PASSWORD_DEFAULT (bcrypt) для хеширования.
     *
     * @noinspection PhpUnused
     * @param array $data Массив данных для вставки/обновления
     * @return array Модифицированный массив с хешированным паролем
     *
     * Пример:
     * $data = ['data' => ['password' => '12345678']];
     * $data = $model->hashPassword($data);
     * // Результат: ['data' => ['password' => '$2y$10$...']]
     */
    protected function hashPassword(array $data): array
    {
        if (isset($data['data']['password'])) {
            $data['data']['password'] = password_hash($data['data']['password'], PASSWORD_DEFAULT);
        }
        return $data;
    }
}