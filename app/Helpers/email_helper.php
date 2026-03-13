<?php

use Config\Services;

if (!function_exists('sendActivationEmail')) {
    /**
     * Отправка письма для подтверждения email
     *
     * @param string $to Email получателя
     * @param string $username Имя пользователя
     * @param string $token Токен активации
     * @return bool Успешность отправки
     */
    function sendActivationEmail(string $to, string $username, string $token): bool
    {
        $email = Services::email();

        $activationLink = site_url("activate/$token");

        $message = view('emails/activation', [
            'username' => $username,
            'link' => $activationLink
        ]);

        $email->setTo($to);
        $email->setSubject('Подтверждение регистрации на E2EE Чате');
        $email->setMessage($message);

        if ($email->send()) {
            log_message('info', "Activation email sent to $to");
            return true;
        } else {
            log_message('error', "Failed to send activation email to $to: " . $email->printDebugger());
            return false;
        }
    }
}

if (!function_exists('sendPasswordResetEmail')) {
    /**
     * Отправка письма для сброса пароля
     *
     * @param string $to Email получателя
     * @param string $token Токен сброса
     * @return bool Успешность отправки
     */
    function sendPasswordResetEmail(string $to, string $token): bool
    {
        $email = Services::email();

        $resetLink = site_url("reset-password/$token");

        $message = view('emails/password_reset', [
            'link' => $resetLink
        ]);

        $email->setTo($to);
        $email->setSubject('Восстановление пароля на E2EE Чате');
        $email->setMessage($message);

        if ($email->send()) {
            log_message('info', "Password reset email sent to $to");
            return true;
        } else {
            log_message('error', "Failed to send password reset email to $to: " . $email->printDebugger());
            return false;
        }
    }
}