<?php

namespace App\Models;

use CodeIgniter\Model;
use ReflectionException;

/**
 * Модель для работы с контактами пользователей
 *
 * Отвечает за добавление, удаление и получение списка контактов.
 * Таблица: contacts
 *
 * @package App\Models
 * @noinspection PhpUnused
 */
class Contact extends Model
{
    /**
     * Имя таблицы в базе данных
     * @var string
     */
    protected $table = 'contacts';

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
     * @var string 'array' - возвращает массивы
     */
    protected $returnType = 'array';

    /**
     * Использование временных меток
     * @var bool true - автоматическое заполнение created_at
     */
    protected $useTimestamps = true;

    /**
     * Поле с датой создания записи
     * @var string
     */
    protected $createdField = 'created_at';

    /**
     * Поле с датой обновления (не используется)
     * @var string
     */
    protected $updatedField = '';

    /**
     * Поля, разрешённые для массового заполнения
     * @var array
     */
    protected $allowedFields = [
        'user_id',     // ID пользователя, который добавляет контакт
        'contact_id'   // ID пользователя, которого добавляют
    ];

    /**
     * Правила валидации
     * @var array
     */
    protected $validationRules = [
        'user_id' => 'required|is_natural_no_zero',
        'contact_id' => 'required|is_natural_no_zero'
    ];

    /**
     * Сообщения об ошибках валидации
     * @var array
     */
    protected $validationMessages = [
        'user_id' => [
            'required' => 'ID пользователя обязателен',
            'is_natural_no_zero' => 'Некорректный ID пользователя'
        ],
        'contact_id' => [
            'required' => 'ID контакта обязателен',
            'is_natural_no_zero' => 'Некорректный ID контакта'
        ]
    ];

    /**
     * Добавление контакта
     *
     * @param int $userId ID пользователя
     * @param int $contactId ID добавляемого контакта
     * @return bool Результат операции
     * @noinspection PhpUnused
     * @throws ReflectionException
     */
    public function addContact(int $userId, int $contactId): bool
    {
        // Нельзя добавить самого себя
        if ($userId === $contactId) {
            log_message('info', "User $userId tried to add themselves as contact");
            return false;
        }

        // Проверяем, существует ли уже такой контакт
        $exists = $this->where('user_id', $userId)
            ->where('contact_id', $contactId)
            ->first();

        if ($exists) {
            log_message('info', "Contact $contactId already exists for user $userId");
            return false;
        }

        // Добавляем контакт
        return $this->insert([
                'user_id' => $userId,
                'contact_id' => $contactId
            ]) !== false;
    }

    /**
     * Удаление контакта
     *
     * @param int $userId ID пользователя
     * @param int $contactId ID удаляемого контакта
     * @return bool Результат операции
     * @noinspection PhpUnused
     */
    public function removeContact(int $userId, int $contactId): bool
    {
        return $this->where('user_id', $userId)
                ->where('contact_id', $contactId)
                ->delete() !== false;
    }

    /**
     * Получение списка контактов пользователя
     *
     * @param int $userId ID пользователя
     * @return array Список контактов с данными пользователей
     * @noinspection PhpUnused
     */
    public function getContacts(int $userId): array
    {
        return $this->select('users.id, users.username, users.email, users.display_name, users.is_active')
            ->join('users', 'users.id = contacts.contact_id')
            ->where('contacts.user_id', $userId)
            ->orderBy('users.display_name', 'ASC')
            ->orderBy('users.username', 'ASC')
            ->findAll();
    }

    /**
     * Проверка, является ли пользователь контактом
     *
     * @param int $userId ID пользователя
     * @param int $contactId ID проверяемого контакта
     * @return bool true если является контактом
     * @noinspection PhpUnused
     */
    public function isContact(int $userId, int $contactId): bool
    {
        return $this->where('user_id', $userId)
                ->where('contact_id', $contactId)
                ->countAllResults() > 0;
    }

    /**
     * Получение количества контактов пользователя
     *
     * @param int $userId ID пользователя
     * @return int Количество контактов
     * @noinspection PhpUnused
     */
    public function getContactCount(int $userId): int
    {
        return $this->where('user_id', $userId)->countAllResults();
    }
}