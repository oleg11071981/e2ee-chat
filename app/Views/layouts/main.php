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
<!-- Основной контент -->
<main class="main-full">
    <div class="container">
        <?= $this->renderSection('content') ?>
    </div>
</main>

<!-- Базовые скрипты -->
<script src="<?= base_url('js/api.js') ?>"></script>
<script src="<?= base_url('js/menu.js') ?>"></script>

<!-- Дополнительные скрипты для страницы -->
<?= $this->renderSection('scripts') ?>
</body>
</html>