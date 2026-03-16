<?php

namespace App\Models;

use CodeIgniter\Model;
use ReflectionException;

/**
 * Модель для работы с сообщениями
 *
 * @package App\Models
 * @noinspection PhpUnused
 */
class Message extends Model
{
    /**
     * Имя таблицы в базе данных
     * @var string
     */
    protected $table = 'messages';

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
     * @var string
     */
    protected $returnType = 'array';

    /**
     * Использование временных меток
     * @var bool
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
        'sender_id',
        'recipient_id',
        'message',
        'is_delivered',
        'is_read'
    ];

    /**
     * Сохранение нового сообщения
     *
     * @param int $senderId
     * @param int $recipientId
     * @param string $message
     * @return int|false ID сообщения или false
     * @throws ReflectionException
     * @noinspection PhpUnused
     */
    public function saveMessage(int $senderId, int $recipientId, string $message): int|false
    {
        $data = [
            'sender_id' => $senderId,
            'recipient_id' => $recipientId,
            'message' => $message,
            'is_delivered' => 0,
            'is_read' => 0
        ];

        if ($this->insert($data)) {
            return $this->getInsertID();
        }

        return false;
    }

    /**
     * Получение новых сообщений для пользователя
     *
     * @param int $userId ID пользователя
     * @param int $lastId ID последнего полученного сообщения
     * @return array
     * @noinspection PhpUnused
     */
    public function getNewMessages(int $userId, int $lastId = 0): array
    {
        return $this->where('recipient_id', $userId)
            ->where('id >', $lastId)
            ->orderBy('id', 'ASC')
            ->findAll();
    }

    /**
     * Получение истории переписки с конкретным пользователем
     *
     * @param int $userId ID текущего пользователя
     * @param int $contactId ID собеседника
     * @param int $limit Количество сообщений
     * @return array
     * @noinspection PhpUnused
     */
    public function getConversation(int $userId, int $contactId, int $limit = 50): array
    {
        return $this->where("(sender_id = ? AND recipient_id = ?) OR (sender_id = ? AND recipient_id = ?)", [
            $userId, $contactId,
            $contactId, $userId
        ])
            ->orderBy('id', 'DESC')
            ->limit($limit)
            ->findAll();
    }

    /**
     * Отметить сообщения как доставленные
     *
     * @param array $messageIds
     * @return bool
     * @throws ReflectionException
     * @noinspection PhpUnused
     */
    public function markAsDelivered(array $messageIds): bool
    {
        if (empty($messageIds)) {
            return true;
        }

        return $this->update($messageIds, ['is_delivered' => 1]);
    }

    /**
     * Отметить сообщение как прочитанное
     *
     * @param int $messageId
     * @return bool
     * @throws ReflectionException
     * @noinspection PhpUnused
     */
    public function markAsRead(int $messageId): bool
    {
        return $this->update($messageId, ['is_read' => 1]);
    }

    /**
     * Получение количества непрочитанных сообщений для пользователя
     *
     * @param int $userId
     * @return int
     * @noinspection PhpUnused
     */
    public function getUnreadCount(int $userId): int
    {
        return $this->where('recipient_id', $userId)
            ->where('is_read', 0)
            ->countAllResults();
    }
}