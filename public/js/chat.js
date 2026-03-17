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
        this.pollInterval = 0;

        /** @type {number} */
        this.errorRetryDelay = 5000;

        // Получаем CSRF токен из meta-тега или cookie
        this.csrfToken = this.getCsrfToken();

        this.startPolling().catch(error => {
            console.error('Polling failed to start:', error);
        });
    }

    /**
     * Получение CSRF токена из meta-тега или cookie
     * @returns {string|null}
     */
    getCsrfToken() {
        // Пытаемся получить из meta-тега
        const metaToken = document.querySelector('meta[name="X-CSRF-TOKEN"]')?.getAttribute('content');
        if (metaToken) return metaToken;

        // Пытаемся получить из cookie
        return this.getCookie('csrf_cookie');
    }

    /**
     * Получение значения cookie по имени
     * @param {string} name - Имя cookie
     * @returns {string|null}
     */
    getCookie(name) {
        const value = `; ${document.cookie}`;
        const parts = value.split(`; ${name}=`);
        if (parts.length === 2) return parts.pop().split(';').shift();
        return null;
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
        const headers = {
            'X-Requested-With': 'XMLHttpRequest'
        };

        // Добавляем CSRF токен, если он есть
        if (this.csrfToken) {
            headers['X-CSRF-TOKEN'] = this.csrfToken;
        }

        const response = await fetch(`/api/chat/poll?last_id=${this.lastId}`, { headers });

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

        const headers = {
            'X-Requested-With': 'XMLHttpRequest'
        };

        // Добавляем CSRF токен, если он есть
        if (this.csrfToken) {
            headers['X-CSRF-TOKEN'] = this.csrfToken;
        }

        const response = await fetch('/api/chat/send', {
            method: 'POST',
            headers: headers,
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
        const headers = {
            'X-Requested-With': 'XMLHttpRequest'
        };

        if (this.csrfToken) {
            headers['X-CSRF-TOKEN'] = this.csrfToken;
        }

        const response = await fetch(`/api/chat/history/${this.contactId}?limit=${limit}`, { headers });

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
        const headers = {
            'X-Requested-With': 'XMLHttpRequest',
            'Content-Type': 'application/json'
        };

        if (this.csrfToken) {
            headers['X-CSRF-TOKEN'] = this.csrfToken;
        }

        const response = await fetch(`/api/chat/read/${messageId}`, {
            method: 'POST',
            headers: headers
        });

        return response.ok;
    }

    /**
     * Получить количество непрочитанных
     * @returns {Promise<number>}
     */
    async getUnreadCount() {
        const headers = {
            'X-Requested-With': 'XMLHttpRequest'
        };

        if (this.csrfToken) {
            headers['X-CSRF-TOKEN'] = this.csrfToken;
        }

        const response = await fetch('/api/chat/unread-count', { headers });

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