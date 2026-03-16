<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $this->renderSection('title') ?> | E2EE Чат</title>

    <!-- Базовые стили -->
    <link rel="stylesheet" href="<?= base_url('css/style.css') ?>">

    <!-- Font Awesome для иконок -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <!-- Дополнительные стили для страницы -->
    <?= $this->renderSection('styles') ?>
</head>
<body>
<!-- Шапка сайта -->
<header class="header">
    <div class="container">
        <div class="logo">
            <a href="<?= base_url('/') ?>">
                <span class="logo-icon">🔐</span>
                <span class="logo-text">E2EE Чат</span>
            </a>
        </div>

        <!-- Кнопка бургер для мобильных -->
        <button class="burger-menu" id="burgerMenu" aria-label="Меню">
            <span></span>
            <span></span>
            <span></span>
        </button>

        <!-- Навигация -->
        <nav class="nav" id="mainNav">
            <?php if (session()->has('is_logged_in')): ?>
                <a href="<?= base_url('logout') ?>" class="btn-logout">Выйти</a>
            <?php else: ?>
                <a href="<?= base_url('login') ?>">Вход</a>
                <a href="<?= base_url('register') ?>">Регистрация</a>
            <?php endif; ?>
        </nav>
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
<script src="<?= base_url('js/menu.js') ?>"></script>

<!-- Дополнительные скрипты для страницы -->
<?= $this->renderSection('scripts') ?>
</body>
</html>