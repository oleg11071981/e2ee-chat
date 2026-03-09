<?php

namespace App\Libraries;

use App\Config\JWT;
use Exception;
use Firebase\JWT\JWT as FirebaseJWT;
use Firebase\JWT\Key;
use RuntimeException;

/**
 * Класс для работы с JWT (JSON Web Tokens)
 *
 * Предоставляет методы для создания, проверки и получения JWT токенов.
 * Использует библиотеку firebase/php-jwt.
 */
class JwtHandler
{
    /**
     * Конфигурация JWT
     *
     * @var JWT Объект с настройками (ключ, алгоритм, время жизни)
     */
    protected JWT $config;

    /**
     * Конструктор класса JwtHandler
     *
     * Инициализирует конфигурацию и проверяет наличие библиотеки Firebase JWT.
     *
     * @throws RuntimeException Если библиотека Firebase JWT не установлена
     */
    public function __construct()
    {
        $this->config = new JWT();

        // Проверяем, что библиотека Firebase JWT установлена
        // (autoload.php уже подключен CodeIgniter в public/index.php)
        if (!class_exists(FirebaseJWT::class)) {
            throw new RuntimeException(
                'Firebase JWT library not installed. Run: composer require firebase/php-jwt'
            );
        }
    }

    /**
     * Генерация JWT токена
     *
     * @param array $data Данные для включения в токен (например, user_id, username)
     * @return string Сгенерированный JWT токен
     *
     * Пример:
     * $token = $jwtHandler->generateToken(['user_id' => 1, 'username' => 'john']);
     */
    public function generateToken(array $data): string
    {
        $payload = [
            'iat' => time(),                          // Время создания
            'exp' => time() + $this->config->expiration, // Время истечения
            'data' => $data                           // Пользовательские данные
        ];

        return FirebaseJWT::encode(
            $payload,
            $this->config->key,
            $this->config->algorithm
        );
    }

    /**
     * Проверка и декодирование JWT токена
     *
     * @param string $token JWT токен для проверки
     * @return object|null Объект с данными из токена или null если токен недействителен
     *
     * Пример:
     * $data = $jwtHandler->validateToken($token);
     * if ($data) {
     *     $userId = $data->data->user_id;
     * }
     */
    public function validateToken(string $token): ?object
    {
        try {
            return FirebaseJWT::decode(
                $token,
                new Key($this->config->key, $this->config->algorithm)
            );
        } catch (Exception) {
            // Любая ошибка (недействительная подпись, истекший токен и т.д.)
            return null;
        }
    }

    /**
     * Извлечение JWT токена из HTTP заголовка Authorization
     *
     * @return string|null Токен или null если заголовок отсутствует или имеет неверный формат
     *
     * Ожидаемый формат заголовка:
     * Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...
     */
    public function getTokenFromHeader(): ?string
    {
        $authHeader = service('request')->getHeaderLine('Authorization');

        if (preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            return $matches[1];
        }

        return null;
    }
}