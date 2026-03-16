/**
 * Класс для управления чатом через Long Polling
 * @class ChatPoller
 */
class ChatPoller {
    /**
     * @constructor
     * @param {number} userId - ID текущего пользователя
     * @param {number} contactId - ID собеседника
     * @param {Object} options - Опции
     * @param {Function} [options.onMessage] - Колбэк при получении сообщений
     * @param {Function} [options.onStatusChange] - Колбэк при изменении статуса
     */
    constructor(userId, contactId, options = {}) {
        /** @type {number} */
        this.userId = userId;

        /** @type {number} */
        this.contactId = contactId;

        /** @type {number} */
        this.lastId = 0;

        /** @type {boolean} */
        this.isPolling = true;

        /** @type {Function|null} */
        this.onMessageCallback = options.onMessage || null;

        /** @type {Function|null} */
        this.onStatusChangeCallback = options.onStatusChange || null;

        /** @type {number} */
        this.pollInterval = 0; // 0 = сразу после ответа

        /** @type {number} */
        this.errorRetryDelay = 5000; // 5 секунд при ошибке

        /** @type {string|null} */
        this.token = localStorage.getItem('access_token');

        // Запускаем polling
        this.startPolling().catch(error => {
            console.error('Polling failed to start:', error);
        });
    }

    /**
     * Запуск long polling цикла
     * @returns {Promise<void>}
     */
    async startPolling() {
        while (this.isPolling) {
            try {
                await this.poll();
                if (this.pollInterval > 0) {
                    await this.sleep(this.pollInterval);
                }
            } catch (error) {
                console.error('Polling error:', error);
                if (this.onStatusChangeCallback) {
                    this.onStatusChangeCallback('error', error.message);
                }
                await this.sleep(this.errorRetryDelay);
            }
        }
    }

    /**
     * Один цикл polling
     * @returns {Promise<Object>}
     * @throws {Error}
     */
    async poll() {
        const response = await fetch(`/api/chat/poll?last_id=${this.lastId}`, {
            headers: {
                'Authorization': `Bearer ${this.token}`,
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        /** @type {Object} */
        const data = await response.json();

        if (data.status === 'success' && data.data && data.data.messages && data.data.messages.length > 0) {
            this.lastId = data.data.last_id;
            if (this.onMessageCallback) {
                this.onMessageCallback(data.data.messages);
            }
        }

        return data;
    }

    /**
     * Отправка сообщения
     * @param {string} text - Текст сообщения
     * @returns {Promise<Object>}
     * @throws {Error}
     */
    async sendMessage(text) {
        const formData = new FormData();
        formData.append('recipient_id', String(this.contactId));
        formData.append('message', String(text));

        const response = await fetch('/api/chat/send', {
            method: 'POST',
            headers: {
                'Authorization': `Bearer ${this.token}`,
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: formData
        });

        /** @type {Object} */
        const data = await response.json();

        if (!response.ok) {
            throw new Error(data.message || 'Ошибка отправки');
        }

        return data;
    }

    /**
     * Загрузка истории сообщений
     * @param {number} [limit=50] - Количество сообщений
     * @returns {Promise<Object>}
     * @throws {Error}
     */
    async loadHistory(limit = 50) {
        const response = await fetch(`/api/chat/history/${this.contactId}?limit=${limit}`, {
            headers: {
                'Authorization': `Bearer ${this.token}`,
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        /** @type {Object} */
        const data = await response.json();

        if (!response.ok) {
            throw new Error(data.message || 'Ошибка загрузки истории');
        }

        if (data.status === 'success' && data.data && data.data.messages && data.data.messages.length > 0) {
            /** @type {Array} */
            const messages = data.data.messages;
            this.lastId = messages[messages.length - 1].id;
        }

        return data.data;
    }

    /**
     * Отметить сообщение как прочитанное
     * @param {number} messageId - ID сообщения
     * @returns {Promise<boolean>}
     */
    async markAsRead(messageId) {
        const response = await fetch(`/api/chat/read/${messageId}`, {
            method: 'POST',
            headers: {
                'Authorization': `Bearer ${this.token}`,
                'X-Requested-With': 'XMLHttpRequest',
                'Content-Type': 'application/json'
            }
        });

        return response.ok;
    }

    /**
     * Получить количество непрочитанных
     * @returns {Promise<number>}
     */
    async getUnreadCount() {
        const response = await fetch('/api/chat/unread-count', {
            headers: {
                'Authorization': `Bearer ${this.token}`,
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        /** @type {Object} */
        const data = await response.json();

        if (response.ok && data.status === 'success' && data.data) {
            return data.data.unread_count || 0;
        }

        return 0;
    }

    /**
     * Остановка polling
     * @returns {void}
     */
    stop() {
        this.isPolling = false;
    }

    /**
     * Смена собеседника
     * @param {number} contactId - ID нового собеседника
     * @returns {void}
     */
    setContact(contactId) {
        this.contactId = contactId;
        this.lastId = 0;
    }

    /**
     * Вспомогательный sleep
     * @param {number} ms - Миллисекунды
     * @returns {Promise<void>}
     */
    sleep(ms) {
        return new Promise(resolve => setTimeout(resolve, ms));
    }
}