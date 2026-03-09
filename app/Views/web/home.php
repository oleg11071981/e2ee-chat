<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Главная<?= $this->endSection() ?>

<?= $this->section('styles') ?>
    <link rel="stylesheet" href="<?= base_url('css/home.css') ?>">
<?= $this->endSection() ?>

<?= $this->section('content') ?>
    <div class="hero">
        <h1>Добро пожаловать в E2EE Чат</h1>
        <p class="hero-subtitle">Защищённое end-to-end шифрование для ваших сообщений</p>

        <div class="hero-buttons">
            <a href="<?= base_url('register') ?>" class="btn btn-primary">Начать общение</a>
            <a href="<?= base_url('login') ?>" class="btn btn-secondary">Войти</a>
        </div>
    </div>

    <div class="features">
        <div class="feature">
            <div class="feature-icon">🔐</div>
            <h3>End-to-End шифрование</h3>
            <p>Ваши сообщения видите только вы и собеседник</p>
        </div>

        <div class="feature">
            <div class="feature-icon">⚡</div>
            <h3>Мгновенная доставка</h3>
            <p>Сообщения приходят в реальном времени</p>
        </div>

        <div class="feature">
            <div class="feature-icon">🛡️</div>
            <h3>Безопасность</h3>
            <p>Современные алгоритмы шифрования</p>
        </div>
    </div>
<?= $this->endSection() ?>