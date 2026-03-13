<?php

namespace App\Models;

use CodeIgniter\Model;
use Random\RandomException;
use ReflectionException;

/**
 * Модель для работы с токенами восстановления пароля
 *
 * Отвечает за создание, проверку и удаление токенов для сброса пароля.
 * Таблица: password_resets
 *
 * @package App\Models
 * @noinspection PhpUnused
 */
class PasswordResetModel extends Model
{
    /**
     * Имя таблицы в базе данных
     * @var string
     */
    protected $table = 'password_resets';

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
     * Поле с датой обновления (не используется, отключаем)
     * @var string
     */
    protected $updatedField = '';

    /**
     * Формат даты и времени
     * @var string
     */
    protected $dateFormat = 'datetime';

    /**
     * Поля, разрешённые для массового заполнения
     * @var array
     */
    protected $allowedFields = [
        'email',      // Email пользователя
        'token',      // Уникальный токен для сброса пароля
        'expires_at'  // Время истечения токена
    ];

    /**
     * Создание токена для сброса пароля
     *
     * Удаляет все старые токены для указанного email
     * и создаёт новый с временем жизни 1 час.
     *
     * @param string $email Email пользователя
     * @return string Сгенерированный токен
     * @throws RandomException|ReflectionException
     * @noinspection PhpUnused
     */
    public function createToken(string $email): string
    {
        // Удаляем старые токены для этого email
        $this->where('email', $email)->delete();

        // Генерируем новый токен (64 символа)
        $token = bin2hex(random_bytes(32));

        // Сохраняем токен в БД
        $this->insert([
            'email' => $email,
            'token' => $token,
            'expires_at' => date('Y-m-d H:i:s', strtotime('+1 hour'))
        ]);

        // Логируем создание токена
        log_message('info', "Password reset token created for email: $email");

        return $token;
    }

    /**
     * Проверка токена
     *
     * Ищет действующий (не истекший) токен в базе данных.
     *
     * @param string $token Токен для проверки
     * @return array|null Массив с данными токена или null, если токен недействителен
     * @noinspection PhpUnused
     */
    public function verifyToken(string $token): ?array
    {
        return $this->where('token', $token)
            ->where('expires_at >', date('Y-m-d H:i:s'))
            ->first();
    }

    /**
     * Удаление использованного токена
     *
     * Вызывается после успешного сброса пароля.
     *
     * @param string $token Токен для удаления
     * @return bool Результат операции
     * @noinspection PhpUnused
     */
    public function deleteToken(string $token): bool
    {
        $result = $this->where('token', $token)->delete();

        if ($result) {
            log_message('info', "Password reset token deleted");
        }

        return $result;
    }

    /**
     * Очистка истекших токенов
     *
     * Удаляет все токены, у которых истёк срок действия.
     * Можно вызывать по расписанию или при каждой проверке.
     *
     * @return int Количество удалённых записей
     * @noinspection PhpUnused
     */
    public function cleanExpiredTokens(): int
    {
        return $this->where('expires_at <', date('Y-m-d H:i:s'))->delete();
    }
}