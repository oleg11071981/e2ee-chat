<?php

namespace App\Controllers\Api;

use App\Models\User;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\RESTful\ResourceController;
use App\Models\Message;
use App\Models\Contact;
use ReflectionException;

/**
 * API для чата с Long Polling
 *
 * @package App\Controllers\Api
 * @noinspection PhpUnused
 */
class ChatController extends ResourceController
{
    protected $format = 'json';

    protected Message $messageModel;
    protected Contact $contactModel;

    public function __construct()
    {
        $this->messageModel = new Message();
        $this->contactModel = new Contact();
    }

    /**
     * Получение ID пользователя из сессии
     */
    protected function getUserId(): ?int
    {
        return session()->get('user_id') ?: null;
    }

    /**
     * Long Polling для получения новых сообщений
     * GET /api/chat/poll
     * @throws ReflectionException
     * @noinspection PhpUnused
     */
    public function poll(): ResponseInterface
    {
        $userId = $this->getUserId();

        if (!$userId) {
            return $this->failUnauthorized('Требуется авторизация');
        }

        $lastId = (int)($this->request->getGet('last_id') ?? 0);
        $timeout = 25;
        $start = time();

        // Закрываем сессию, чтобы не блокировать другие запросы
        session_write_close();

        while (time() - $start < $timeout) {
            $messages = $this->messageModel
                ->where('recipient_id', $userId)
                ->where('id >', $lastId)
                ->orderBy('id', 'ASC')
                ->findAll();

            if (!empty($messages)) {
                $messageIds = array_column($messages, 'id');

                // Асинхронно отмечаем как доставленные (в фоне)
                $this->messageModel->markAsDelivered($messageIds);

                return $this->respond([
                    'status' => 'success',
                    'data' => [
                        'messages' => $messages,
                        'last_id' => end($messages)['id']
                    ]
                ]);
            }

            sleep(1);
        }

        return $this->respond([
            'status' => 'success',
            'data' => [
                'messages' => [],
                'last_id' => $lastId
            ]
        ]);
    }

    /**
     * Отправка нового сообщения
     * POST /api/chat/send
     */
    public function send(): ResponseInterface
    {
        $userId = $this->getUserId();

        if (!$userId) {
            return $this->failUnauthorized('Требуется авторизация');
        }

        $rules = [
            'recipient_id' => 'required|is_natural_no_zero',
            'message' => 'required|min_length[1]|max_length[5000]'
        ];

        if (!$this->validate($rules)) {
            return $this->failValidationErrors($this->validator->getErrors());
        }

        $recipientId = (int)$this->request->getPost('recipient_id');
        $messageText = trim($this->request->getPost('message'));

        // Проверяем, что получатель существует
        $userModel = new User();
        $recipient = $userModel->find($recipientId);

        if (!$recipient) {
            return $this->failNotFound('Получатель не найден');
        }

        // Проверяем, что это контакт
        $isContact = $this->contactModel->isContact($userId, $recipientId);
        if (!$isContact) {
            return $this->fail('Можно отправлять сообщения только контактам', 403);
        }

        try {
            $messageId = $this->messageModel->saveMessage(
                $userId,
                $recipientId,
                $messageText
            );

            if ($messageId) {
                return $this->respond([
                    'status' => 'success',
                    'data' => [
                        'message_id' => $messageId,
                        'sent_at' => date('Y-m-d H:i:s')
                    ]
                ]);
            }

            return $this->failServerError('Не удалось отправить сообщение');

        } catch (ReflectionException) {
            return $this->failServerError('Ошибка базы данных');
        }
    }

    /**
     * Получение истории сообщений с контактом
     * GET /api/chat/history/{contactId}
     */
    public function history($contactId = null): ResponseInterface
    {
        $userId = $this->getUserId();

        if (!$userId) {
            return $this->failUnauthorized('Требуется авторизация');
        }

        $contactId = (int)$contactId;

        if ($contactId <= 0) {
            return $this->failValidationErrors('Некорректный ID контакта');
        }

        $limit = (int)($this->request->getGet('limit') ?? 50);
        $limit = min($limit, 100);

        $messages = $this->messageModel->getConversation($userId, $contactId, $limit);

        return $this->respond([
            'status' => 'success',
            'data' => [
                'messages' => array_reverse($messages),
                'count' => count($messages)
            ]
        ]);
    }

    /**
     * Отметить сообщение как прочитанное
     * POST /api/chat/read/{messageId}
     * @noinspection PhpUnused
     */
    public function markRead($messageId = null): ResponseInterface
    {
        $userId = $this->getUserId();

        if (!$userId) {
            return $this->failUnauthorized('Требуется авторизация');
        }

        $messageId = (int)$messageId;

        if ($messageId <= 0) {
            return $this->failValidationErrors('Некорректный ID сообщения');
        }

        $message = $this->messageModel->find($messageId);

        if (!$message) {
            return $this->failNotFound('Сообщение не найдено');
        }

        if ($message['recipient_id'] != $userId) {
            return $this->failForbidden('Нет прав на это действие');
        }

        try {
            $this->messageModel->markAsRead($messageId);

            return $this->respond([
                'status' => 'success',
                'message' => 'Сообщение отмечено как прочитанное'
            ]);

        } catch (ReflectionException) {
            return $this->failServerError('Ошибка базы данных');
        }
    }

    /**
     * Получение количества непрочитанных сообщений
     * GET /api/chat/unread-count
     * @noinspection PhpUnused
     */
    public function unreadCount(): ResponseInterface
    {
        $userId = $this->getUserId();

        if (!$userId) {
            return $this->failUnauthorized('Требуется авторизация');
        }

        $count = $this->messageModel->getUnreadCount($userId);

        return $this->respond([
            'status' => 'success',
            'data' => [
                'unread_count' => $count
            ]
        ]);
    }
}