<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 - Доступ запрещён</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary-color: #667eea;
            --danger-color: #f56565;
            --danger-dark: #c53030;
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

        .error-container {
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

        .error-code {
            font-size: 120px;
            font-weight: 700;
            line-height: 1;
            color: var(--danger-color);
            margin-bottom: 10px;
            text-shadow: 0 10px 20px rgba(229, 62, 62, 0.2);
        }

        .error-title {
            font-size: 32px;
            color: var(--text-color);
            margin-bottom: 15px;
        }

        .error-message {
            color: var(--text-light);
            font-size: 16px;
            margin-bottom: 30px;
            line-height: 1.6;
        }

        .divider {
            width: 60px;
            height: 4px;
            background: linear-gradient(90deg, var(--danger-color), #fc8181);
            margin: 25px auto;
            border-radius: 2px;
        }

        .error-actions {
            display: flex;
            gap: 15px;
            justify-content: center;
            flex-wrap: wrap;
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
        }

        .btn-primary {
            background: var(--primary-color);
            color: white;
        }

        .btn-primary:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4);
        }

        .btn-outline {
            background: transparent;
            color: var(--danger-color);
            border: 2px solid var(--danger-color);
        }

        .btn-outline:hover {
            background: var(--danger-color);
            color: white;
        }

        .icon-403 {
            font-size: 80px;
            color: var(--danger-color);
            margin-bottom: 20px;
        }

        .warning-note {
            background: #fff5f5;
            border: 1px solid #fed7d7;
            border-radius: 8px;
            padding: 12px 16px;
            margin-top: 20px;
            color: #742a2a;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 10px;
            text-align: left;
        }

        .warning-note i {
            font-size: 20px;
            color: var(--danger-color);
        }

        @media (max-width: 768px) {
            .error-container {
                padding: 40px 30px;
            }

            .error-code {
                font-size: 100px;
            }

            .error-title {
                font-size: 28px;
            }

            .error-actions {
                flex-direction: column;
            }

            .btn {
                width: 100%;
            }
        }

        @media (max-width: 480px) {
            .error-container {
                padding: 30px 20px;
            }

            .error-code {
                font-size: 80px;
            }

            .error-title {
                font-size: 24px;
            }
        }
    </style>
</head>
<body>
<div class="error-container">
    <div class="icon-403">
        <i class="fas fa-shield-halved"></i>
    </div>
    <div class="error-code">403</div>
    <h1 class="error-title">Доступ запрещён</h1>
    <div class="error-message">
        <p>У вас нет прав для просмотра этой страницы.</p>
        <p>Это может быть связано с попыткой доступа к системным файлам или папкам.</p>
    </div>

    <div class="divider"></div>

    <div class="error-actions">
        <a href="<?= base_url('/') ?>" class="btn btn-primary">
            <i class="fas fa-home"></i> На главную
        </a>
        <a href="<?= base_url('login') ?>" class="btn btn-outline">
            <i class="fas fa-sign-in-alt"></i> Войти
        </a>
    </div>

    <div class="warning-note">
        <i class="fas fa-circle-exclamation"></i>
        <div>
            <strong>Это нормально!</strong> Доступ к системным папкам заблокирован для вашей безопасности.
            Если вы пытались попасть на реальную страницу, убедитесь, что правильно ввели адрес.
        </div>
    </div>
</div>
</body>
</html>