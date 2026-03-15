<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>400 - Неверный запрос</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary-color: #667eea;
            --warning-color: #ecc94b;
            --text-color: #2d3748;
            --text-light: #718096;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
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
            color: var(--warning-color);
            margin-bottom: 10px;
            text-shadow: 0 10px 20px rgba(236, 201, 75, 0.2);
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
            background: linear-gradient(90deg, var(--warning-color), #fbbf24);
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
            background: #5a67d8;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4);
        }

        .btn-outline {
            background: transparent;
            color: var(--warning-color);
            border: 2px solid var(--warning-color);
        }

        .btn-outline:hover {
            background: var(--warning-color);
            color: white;
        }

        .icon-400 {
            font-size: 80px;
            color: var(--warning-color);
            margin-bottom: 20px;
        }

        .warning-note {
            background: #fef3c7;
            border: 1px solid #fde68a;
            border-radius: 8px;
            padding: 12px 16px;
            margin-top: 20px;
            color: #92400e;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 10px;
            text-align: left;
        }

        .warning-note i {
            font-size: 20px;
            color: var(--warning-color);
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
    <div class="icon-400">
        <i class="fas fa-exclamation-triangle"></i>
    </div>
    <div class="error-code">400</div>
    <h1 class="error-title">Неверный запрос</h1>
    <div class="error-message">
        <p>Запрос содержит недопустимые символы или не может быть обработан.</p>
        <p>Проверьте правильность введённого адреса.</p>
    </div>

    <div class="divider"></div>

    <div class="error-actions">
        <a href="/" class="btn btn-primary">
            <i class="fas fa-home"></i> На главную
        </a>
        <a href="javascript:history.back()" class="btn btn-outline">
            <i class="fas fa-arrow-left"></i> Вернуться
        </a>
    </div>

    <div class="warning-note">
        <i class="fas fa-info-circle"></i>
        <div>
            <strong>Это нормально!</strong> Ошибка 400 часто возникает при автоматических запросах от расширений браузера или краулеров. Если вы перешли по ссылке с сайта, сообщите администратору.
        </div>
    </div>
</div>
</body>
</html>