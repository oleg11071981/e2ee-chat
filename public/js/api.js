/**
 * API клиент для работы с бэкендом
 *
 * Предоставляет методы для взаимодействия с REST API:
 * - Аутентификация (регистрация, вход, выход)
 * - Работа с профилем пользователя
 *
 * @see AuthController.php
 */

class ApiClient {
    /**
     * Базовый URL API
     * @type {string}
     */
    baseUrl = '/api';

    /**
     * Конструктор класса
     * @param {string} [baseUrl] - Базовый URL API (опционально)
     */
    constructor(baseUrl) {
        this.baseUrl = baseUrl || '/api';
        this.token = localStorage.getItem('access_token');
    }

    /**
     * Установка токена авторизации
     * @param {string} token - JWT токен
     */
    setToken(token) {
        this.token = token;
        localStorage.setItem('access_token', token);
    }

    /**
     * Получение токена авторизации
     * @returns {string|null} JWT токен или null
     */
    getToken() {
        return this.token || localStorage.getItem('access_token');
    }

    /**
     * Удаление токена авторизации
     */
    removeToken() {
        this.token = null;
        localStorage.removeItem('access_token');
        localStorage.removeItem('refresh_token');
    }

    /**
     * Базовый метод для HTTP запросов
     * @param {string} endpoint - Эндпоинт (например, '/auth/login')
     * @param {Object} options - Опции запроса (method, body, headers)
     * @returns {Promise<Object>} Ответ сервера
     * @throws {Error} Ошибка запроса
     */
    async request(endpoint, options = {}) {
        const url = `${this.baseUrl}${endpoint}`;
        const token = this.getToken();

        const headers = {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            ...options.headers
        };

        if (token) {
            headers['Authorization'] = `Bearer ${token}`;
        }

        const config = {
            ...options,
            headers
        };

        try {
            const response = await fetch(url, config);
            const data = await response.json();

            if (!response.ok) {
                // Если токен истёк (401) - пробуем обновить
                if (response.status === 401 && token) {
                    const refreshed = await this.refreshToken();
                    if (refreshed) {
                        // Повторяем запрос с новым токеном
                        return this.request(endpoint, options);
                    }
                }

                // Формируем понятную ошибку
                const error = new Error(data.message || 'Ошибка запроса');
                error.status = response.status;
                error.data = data;
                throw error;
            }

            return data;
        } catch (error) {
            console.error('API Request Error:', error);
            throw error;
        }
    }

    /**
     * Обновление JWT токена
     * @returns {Promise<boolean>} Успешно ли обновлён токен
     */
    async refreshToken() {
        const refreshToken = localStorage.getItem('refresh_token');
        if (!refreshToken) return false;

        try {
            const response = await fetch(`${this.baseUrl}/auth/refresh`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ refresh_token: refreshToken })
            });

            const data = await response.json();

            if (response.ok && data.data?.access_token) {
                this.setToken(data.data.access_token);
                if (data.data.refresh_token) {
                    localStorage.setItem('refresh_token', data.data.refresh_token);
                }
                return true;
            }
        } catch (error) {
            console.error('Token refresh failed:', error);
        }

        this.removeToken();
        return false;
    }

    /**
     * Проверка авторизации
     * @returns {boolean} Есть ли токен
     */
    isAuthenticated() {
        return !!this.getToken();
    }

    // ============================================
    // Аутентификация
    // ============================================

    /**
     * Регистрация нового пользователя
     * @param {Object} data - Данные пользователя
     * @param {string} data.username - Имя пользователя
     * @param {string} data.email - Email
     * @param {string} data.password - Пароль
     * @returns {Promise<Object>} Ответ сервера
     */
    async register(data) {
        return this.request('/auth/register', {
            method: 'POST',
            body: JSON.stringify(data)
        });
    }

    /**
     * Вход в систему
     * @param {string} login - Email или имя пользователя
     * @param {string} password - Пароль
     * @returns {Promise<Object>} Ответ с токеном и данными пользователя
     */
    async login(login, password) {
        const data = await this.request('/auth/login', {
            method: 'POST',
            body: JSON.stringify({ login, password })
        });

        if (data.data?.access_token) {
            this.setToken(data.data.access_token);
            if (data.data.refresh_token) {
                localStorage.setItem('refresh_token', data.data.refresh_token);
            }
        }

        return data;
    }

    /**
     * Выход из системы
     * @returns {Promise<Object>} Ответ сервера
     */
    async logout() {
        const data = await this.request('/auth/logout', {
            method: 'POST'
        });
        this.removeToken();
        return data;
    }

    // ============================================
    // Профиль пользователя
    // ============================================

    /**
     * Получение профиля текущего пользователя
     * @returns {Promise<Object>} Данные пользователя
     */
    async getProfile() {
        return this.request('/profile', {
            method: 'GET'
        });
    }

    /**
     * Обновление профиля пользователя
     * @param {Object} data - Данные для обновления
     * @returns {Promise<Object>} Ответ сервера
     */
    async updateProfile(data) {
        return this.request('/profile', {
            method: 'PUT',
            body: JSON.stringify(data)
        });
    }

    // ============================================
    // Работа с пользователями
    // ============================================

    /**
     * Получение списка пользователей
     * @param {string} [search] - Поисковый запрос
     * @returns {Promise<Object>} Список пользователей
     */
    async getUsers(search = '') {
        const query = search ? `?search=${encodeURIComponent(search)}` : '';
        return this.request('/users' + query, {
            method: 'GET'
        });
    }

    /**
     * Получение информации о пользователе
     * @param {number} userId - ID пользователя
     * @returns {Promise<Object>} Данные пользователя
     */
    async getUser(userId) {
        return this.request(`/users/${userId}`, {
            method: 'GET'
        });
    }
}

// Создаём глобальный экземпляр для использования на страницах
const api = new ApiClient();