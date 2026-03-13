<?php
/**
 * @var string $username Имя пользователя
 * @var string $link Ссылка для активации
 */
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: #667eea;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 5px 5px 0 0;
        }
        .content {
            background: #f9f9f9;
            padding: 30px;
            border-radius: 0 0 5px 5px;
        }
        .button {
            display: inline-block;
            padding: 12px 24px;
            background: #667eea;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
        }
        .button:hover {
            background: #5a67d8;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            color: #999;
            font-size: 12px;
        }
    </style>
    <title>Активация по e-mail</title>
</head>
<body>
<div class="header">
    <h1>E2EE Чат</h1>
</div>

<div class="content">
    <h2>Здравствуйте, <?= esc($username) ?>!</h2>

    <p>Спасибо за регистрацию в нашем защищённом мессенджере!</p>

    <p>Для завершения регистрации и активации вашего аккаунта, пожалуйста, подтвердите свой email, перейдя по ссылке:</p>

    <p style="text-align: center;">
        <a href="<?= $link ?>" class="button">Подтвердить email</a>
    </p>

    <p>Если кнопка не работает, скопируйте и вставьте в браузер следующую ссылку:</p>
    <p style="word-break: break-all; color: #667eea;"><?= $link ?></p>

    <p>Ссылка действительна в течение 24 часов.</p>

    <p>Если вы не регистрировались на нашем сайте, просто проигнорируйте это письмо.</p>
</div>

<div class="footer">
    &copy; <?= date('Y') ?> E2EE Чат. Все права защищены.<br>
    Это автоматическое письмо, пожалуйста, не отвечайте на него.
</div>
</body>
</html>