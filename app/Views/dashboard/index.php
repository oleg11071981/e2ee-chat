<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Личный кабинет<?= $this->endSection() ?>

<?= $this->section('styles') ?>
    <link rel="stylesheet" href="<?= base_url('css/dashboard.css') ?>">
<?= $this->endSection() ?>

<?= $this->section('content') ?>
    <div class="dashboard-container">
        <!-- Приветственная секция -->
        <div class="welcome-section">
            <h1>Добро пожаловать, <?= esc($user['username'] ?? 'Гость') ?>! 👋</h1>
            <p class="welcome-text">Рады видеть вас в защищённом мессенджере</p>
        </div>

        <!-- Статистика -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">📅</div>
                <div class="stat-content">
                    <span class="stat-label">На сайте с</span>
                    <span class="stat-value"><?= isset($user['created_at']) ? date('d.m.Y', strtotime($user['created_at'])) : '—' ?></span>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">💬</div>
                <div class="stat-content">
                    <span class="stat-label">Сообщений</span>
                    <span class="stat-value">0</span>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">👥</div>
                <div class="stat-content">
                    <span class="stat-label">Контактов</span>
                    <span class="stat-value"><?= $contactCount ?? 0 ?></span>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">🔐</div>
                <div class="stat-content">
                    <span class="stat-label">Статус</span>
                    <span class="stat-value">Онлайн</span>
                </div>
            </div>
        </div>

        <!-- Быстрые действия -->
        <div class="actions-section">
            <h2>Быстрые действия</h2>
            <div class="actions-grid">
                <!-- Контакты -->
                <a href="<?= base_url('contacts') ?>" class="action-card">
                    <div class="action-icon">👥</div>
                    <h3>Контакты</h3>
                    <p>Управление списком контактов</p>
                </a>

                <!-- Профиль -->
                <a href="<?= base_url('dashboard/profile') ?>" class="action-card">
                    <div class="action-icon">⚙️</div>
                    <h3>Профиль</h3>
                    <p>Настройки отображаемого имени</p>
                </a>

                <!-- Заглушки для нерабочих страниц -->
                <div class="action-card" style="opacity: 0.7; cursor: default;">
                    <div class="action-icon">💬</div>
                    <h3>Чат (в разработке)</h3>
                    <p>Скоро будет доступен</p>
                </div>

                <div class="action-card" style="opacity: 0.7; cursor: default;">
                    <div class="action-icon">🛡️</div>
                    <h3>Безопасность</h3>
                    <p>В разработке</p>
                </div>
            </div>
        </div>

        <!-- Информация о пользователе -->
        <div class="info-section">
            <h2>Информация об аккаунте</h2>
            <div class="info-grid">
                <div class="info-item">
                    <span class="info-label">Имя пользователя:</span>
                    <span class="info-value"><?= esc($user['username'] ?? '—') ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Email:</span>
                    <span class="info-value"><?= esc($user['email'] ?? '—') ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">ID пользователя:</span>
                    <span class="info-value">#<?= $user['id'] ?? '—' ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Дата регистрации:</span>
                    <span class="info-value"><?= isset($user['created_at']) ? date('d.m.Y H:i', strtotime($user['created_at'])) : '—' ?></span>
                </div>
            </div>
        </div>
    </div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
    <script src="<?= base_url('js/dashboard.js') ?>"></script>
<?= $this->endSection() ?>