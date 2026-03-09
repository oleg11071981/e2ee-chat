<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $this->renderSection('title') ?> | E2EE Чат</title>

    <!-- Базовые стили -->
    <link rel="stylesheet" href="<?= base_url('css/style.css') ?>">

    <!-- Дополнительные стили для страницы -->
    <?= $this->renderSection('styles') ?>
</head>
<body class="auth-layout">
<!-- Только контент, без шапки и подвала -->
<?= $this->renderSection('content') ?>

<!-- Базовые скрипты -->
<script src="<?= base_url('js/api.js') ?>"></script>

<!-- Дополнительные скрипты для страницы -->
<?= $this->renderSection('scripts') ?>
</body>
</html>