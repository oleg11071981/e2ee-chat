<?php
/**
 * @var string $pageName Название страницы
 * @var string $pageUrl URL страницы (опционально)
 * @var string $icon Иконка Font Awesome (опционально)
 */
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($pageName) ?> - В разработке</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary-color: #667eea;
            --primary-dark: #5a67d8;
            --secondary-color: #764ba2;
            --text-color: #2d3748;
            --text-light: #718096;
            --bg-light: #f7fafc;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: var(--bg-light);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .placeholder-container {
            text-align: center;
            max-width: 500px;
            width: 100%;
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1);
            padding: 50px 40px;
            animation: slideUp 0.5s ease;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .construction-icon {
            font-size: 80px;
            color: var(--primary-color);
            margin-bottom: 20px;
            animation: bounce 2s infinite;
        }

        @keyframes bounce {
            0%, 20%, 50%, 80%, 100% {
                transform: translateY(0);
            }
            40% {
                transform: translateY(-20px);
            }
            60% {
                transform: translateY(-10px);
            }
        }

        .title {
            font-size: 32px;
            color: var(--text-color);
            margin-bottom: 15px;
        }

        .message {
            color: var(--text-light);
            font-size: 16px;
            margin-bottom: 30px;
            line-height: 1.6;
        }

        .progress-container {
            margin: 30px 0;
            text-align: left;
        }

        .progress-label {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            color: var(--text-color);
            font-weight: 500;
        }

        .progress-bar {
            height: 10px;
            background: #e2e8f0;
            border-radius: 5px;
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            width: 30%;
            background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
            border-radius: 5px;
            animation: progressPulse 2s infinite;
        }

        @keyframes progressPulse {
            0% {
                opacity: 1;
            }
            50% {
                opacity: 0.7;
            }
            100% {
                opacity: 1;
            }
        }

        .features-list {
            text-align: left;
            margin: 30px 0;
            padding: 20px;
            background: var(--bg-light);
            border-radius: 10px;
        }

        .features-list h3 {
            color: var(--text-color);
            margin-bottom: 15px;
            font-size: 18px;
        }

        .features-list ul {
            list-style: none;
            padding: 0;
        }

        .features-list li {
            padding: 8px 0;
            color: var(--text-light);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .features-list li i {
            color: var(--primary-color);
            width: 20px;
        }

        .features-list li.completed i {
            color: #48bb78;
        }

        .features-list li.in-progress i {
            color: #ecc94b;
        }

        .btn {
            display: inline-block;
            padding: 12px 30px;
            font-size: 16px;
            font-weight: 600;
            text-align: center;
            text-decoration: none;
            border-radius: 8px;
            transition: all 0.3s;
            border: none;
            cursor: pointer;
            background: var(--primary-color);
            color: white;
            margin: 5px;
        }

        .btn:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4);
        }

        .btn-outline {
            background: transparent;
            color: var(--primary-color);
            border: 2px solid var(--primary-color);
        }

        .btn-outline:hover {
            background: var(--primary-color);
            color: white;
        }

        .divider {
            width: 60px;
            height: 4px;
            background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
            margin: 25px auto;
            border-radius: 2px;
        }

        @media (max-width: 768px) {
            .placeholder-container {
                padding: 40px 30px;
            }

            .title {
                font-size: 28px;
            }

            .btn {
                width: 100%;
                margin: 5px 0;
            }

            .btn-group {
                display: flex;
                flex-direction: column;
            }
        }

        @media (max-width: 480px) {
            .placeholder-container {
                padding: 30px 20px;
            }

            .title {
                font-size: 24px;
            }

            .construction-icon {
                font-size: 60px;
            }
        }
    </style>
</head>
<body>
<div class="placeholder-container">
    <div class="construction-icon">
        <i class="fas <?= isset($icon) ? esc($icon) : 'fa-code' ?>"></i>
    </div>

    <h1 class="title"><?= esc($pageName) ?></h1>

    <div class="message">
        <p>Этот раздел находится в разработке.</p>
        <p>Мы работаем над ним и скоро он станет доступен!</p>
    </div>

    <div class="progress-container">
        <div class="progress-label">
            <span>Готовность</span>
            <span>30%</span>
        </div>
        <div class="progress-bar">
            <div class="progress-fill"></div>
        </div>
    </div>

    <div class="features-list">
        <h3>Что будет доступно:</h3>
        <ul>
            <li class="completed">
                <i class="fas fa-check-circle"></i>
                <span>Базовая структура</span>
            </li>
            <li class="in-progress">
                <i class="fas fa-spinner fa-pulse"></i>
                <span>Основной функционал</span>
            </li>
            <li>
                <i class="far fa-circle"></i>
                <span>Продвинутые настройки</span>
            </li>
            <li>
                <i class="far fa-circle"></i>
                <span>Интеграции</span>
            </li>
        </ul>
    </div>

    <div class="divider"></div>

    <div class="btn-group">
        <a href="javascript:history.back()" class="btn btn-outline">
            <i class="fas fa-arrow-left"></i> Вернуться
        </a>
        <a href="<?= base_url('dashboard') ?>" class="btn">
            <i class="fas fa-tachometer-alt"></i> В личный кабинет
        </a>
    </div>
</div>
</body>
</html>