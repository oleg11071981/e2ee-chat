<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use App\Libraries\JwtHandler;

/**
 * Фильтр JWT аутентификации
 *
 * Этот фильтр проверяет наличие и валидность JWT токена в запросах к API.
 * Добавляется к защищённым маршрутам в файле Routes.php.
 *
 * @package App\Filters
 * @see https://codeigniter.com/user_guide/incoming/filters.html Документация CI4 Filters
 */
class JWTAuth implements FilterInterface
{
    /**
     * Обработчик JWT
     *
     * @var JwtHandler Экземпляр класса для работы с JWT токенами
     */
    protected JwtHandler $jwt;

    /**
     * Конструктор фильтра
     *
     * Инициализирует обработчик JWT для использования в методах фильтра.
     */
    public function __construct()
    {
        $this->jwt = new JwtHandler();
    }

    /**
     * Действия, выполняемые ДО вызова контроллера
     *
     * Проверяет наличие и валидность JWT токена в заголовке Authorization.
     * Если токен отсутствует или недействителен - возвращает ошибку 401.
     * Если токен валиден - добавляет данные пользователя в заголовки запроса.
     *
     * @param RequestInterface $request Объект HTTP запроса
     * @param array|null $arguments Дополнительные аргументы (не используются)
     * @return RequestInterface|ResponseInterface Запрос (продолжаем) или ответ (ошибка)
     *
     * Успешный результат:
     * - Добавляет заголовки:
     *   - X-User-Id: ID пользователя из токена
     *   - X-User-Data: JSON с данными пользователя
     *
     * Ошибки:
     * - 401 Unauthorized - токен отсутствует
     * - 401 Unauthorized - токен недействителен или истёк
     */
    public function before(RequestInterface $request, $arguments = null): ResponseInterface|RequestInterface
    {
        // 1. Получаем токен из заголовка Authorization
        $token = $this->jwt->getTokenFromHeader();

        // 2. Проверяем наличие токена
        if (!$token) {
            return service('response')
                ->setJSON([
                    'status' => 'error',
                    'message' => 'Токен не предоставлен'
                ])
                ->setStatusCode(ResponseInterface::HTTP_UNAUTHORIZED);
        }

        // 3. Проверяем валидность токена
        $payload = $this->jwt->validateToken($token);

        if (!$payload) {
            return service('response')
                ->setJSON([
                    'status' => 'error',
                    'message' => 'Недействительный или истекший токен'
                ])
                ->setStatusCode(ResponseInterface::HTTP_UNAUTHORIZED);
        }

        // 4. Токен валиден - передаём данные пользователя в контроллер через заголовки
        // Это необходимо для PHP 8.2+, так как нельзя динамически добавлять свойства
        $request = $request->setHeader('X-User-Id', (string) $payload->data->user_id);
        return $request->setHeader('X-User-Data', json_encode($payload->data));
    }

    /**
     * Действия, выполняемые ПОСЛЕ вызова контроллера
     *
     * В данном фильтре пост-обработка не требуется.
     * Метод оставлен для реализации интерфейса FilterInterface.
     *
     * @param RequestInterface $request Объект HTTP запроса
     * @param ResponseInterface $response Объект HTTP ответа
     * @param array|null $arguments Дополнительные аргументы (не используются)
     * @return void
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Ничего не делаем после выполнения контроллера
        // Все необходимые проверки выполнены в before()
    }
}