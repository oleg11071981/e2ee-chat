<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Главная<?= $this->endSection() ?>

<?= $this->section('styles') ?>
    <link rel="stylesheet" href="<?= base_url('css/home.css') ?>">
<?= $this->endSection() ?>

<?= $this->section('content') ?>
    <div class="home-container">
        <div class="hero">
            <h1>🔐 Добро пожаловать в E2EE Чат</h1>
            <p class="hero-subtitle">Защищённое end-to-end шифрование для ваших сообщений</p>

            <div class="hero-buttons">
                <a href="<?= base_url('register') ?>" class="btn btn-primary">Начать общение</a>
                <a href="<?= base_url('login') ?>" class="btn btn-secondary">Войти</a>
            </div>
        </div>
    </div>
<?= $this->endSection() ?>