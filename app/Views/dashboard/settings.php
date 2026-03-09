<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Настройки<?= $this->endSection() ?>

<?= $this->section('styles') ?>
    <link rel="stylesheet" href="<?= base_url('css/dashboard.css') ?>">
<?= $this->endSection() ?>

<?= $this->section('content') ?>
    <div class="dashboard-container">
        <div class="page-header">
            <h1>Настройки</h1>
            <a href="<?= base_url('dashboard') ?>" class="btn btn-secondary">← Назад</a>
        </div>

        <?php if (session()->has('success')): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                <p><?= session('success') ?></p>
            </div>
        <?php endif; ?>

        <?php if (session()->has('errors')): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i>
                <div>
                    <?php foreach (session('errors') as $error): ?>
                        <p><?= esc($error) ?></p>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <div class="settings-section">
            <h2>Настройки чата</h2>
            <p class="text-muted">Раздел в разработке</p>
            <div class="settings-placeholder">
                <p>Здесь будут настройки:</p>
                <ul>
                    <li>Уведомления о новых сообщениях</li>
                    <li>Звуковые оповещения</li>
                    <li>Автосохранение истории</li>
                </ul>
            </div>
        </div>

        <div class="settings-section">
            <h2>Настройки уведомлений</h2>
            <p class="text-muted">Раздел в разработке</p>
            <div class="settings-placeholder">
                <p>Здесь будут настройки:</p>
                <ul>
                    <li>Email уведомления</li>
                    <li>Push уведомления в браузере</li>
                    <li>Не беспокоить</li>
                </ul>
            </div>
        </div>

        <div class="settings-section">
            <h2>Конфиденциальность</h2>
            <p class="text-muted">Раздел в разработке</p>
            <div class="settings-placeholder">
                <p>Здесь будут настройки:</p>
                <ul>
                    <li>Кто может писать мне сообщения</li>
                    <li>Блокировка пользователей</li>
                    <li>Видимость онлайн-статуса</li>
                </ul>
            </div>
        </div>

        <div class="settings-section">
            <h2>Безопасность</h2>
            <p class="text-muted">Раздел в разработке</p>
            <div class="settings-placeholder">
                <p>Здесь будут настройки:</p>
                <ul>
                    <li>Двухфакторная аутентификация</li>
                    <li>Смена пароля</li>
                    <li>История входов</li>
                </ul>
            </div>
        </div>
    </div>
<?= $this->endSection() ?>