<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $this->renderSection('title') ?> | E2EE Чат</title>

    <!-- Базовые стили -->
    <link rel="stylesheet" href="<?= base_url('css/style.css') ?>">

    <!-- Дополнительные стили для конкретной страницы -->
    <?= $this->renderSection('styles') ?>
</head>
<body>
<!-- Шапка сайта -->
<header class="header">
    <div class="container">
        <div class="logo">
            <a href="<?= base_url('/') ?>">🔐 E2EE Чат</a>
        </div>

        <?php if (session()->has('is_logged_in')): ?>
            <nav class="nav">
                <a href="<?= base_url('dashboard') ?>">Личный кабинет</a>
                <a href="<?= base_url('logout') ?>" class="btn-logout">Выйти</a>
            </nav>
        <?php else: ?>
            <nav class="nav">
                <a href="<?= base_url('login') ?>">Вход</a>
                <a href="<?= base_url('register') ?>">Регистрация</a>
            </nav>
        <?php endif; ?>
    </div>
</header>

<!-- Основной контент -->
<main class="main">
    <div class="container">
        <?= $this->renderSection('content') ?>
    </div>
</main>

<!-- Подвал -->
<footer class="footer">
    <div class="container">
        <p>&copy; <?= date('Y') ?> E2EE Чат. Все права защищены.</p>
    </div>
</footer>

<!-- Базовые скрипты -->
<script src="<?= base_url('js/api.js') ?>"></script>

<!-- Дополнительные скрипты для страницы -->
<?= $this->renderSection('scripts') ?>
</body>
</html>