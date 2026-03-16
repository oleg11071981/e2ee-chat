<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Поиск пользователей<?= $this->endSection() ?>

<?= $this->section('styles') ?>
    <link rel="stylesheet" href="<?= base_url('css/contacts.css') ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php
/**
 * @var string $query Поисковый запрос
 * @var array $users Найденные пользователи
 */
?>
    <div class="contacts-container">
        <div class="contacts-header">
            <h1>Поиск пользователей</h1>
            <div class="header-actions">
                <a href="<?= base_url('contacts') ?>" class="btn btn-secondary">
                    <i class="fas fa-address-book"></i> Мои контакты
                </a>
                <a href="<?= base_url('dashboard') ?>" class="btn btn-outline">
                    <i class="fas fa-arrow-left"></i> Назад
                </a>
            </div>
        </div>

        <?php if (session()->has('success')): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                <p><?= session('success') ?></p>
            </div>
        <?php endif; ?>

        <?php if (session()->has('error')): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i>
                <p><?= session('error') ?></p>
            </div>
        <?php endif; ?>

        <div class="search-section">
            <form action="<?= base_url('contacts/search') ?>" method="GET" class="search-form">
                <div class="search-wrapper">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text"
                           name="q"
                           id="search-query"
                           value="<?= esc($query) ?>"
                           placeholder="Введите имя или email..."
                           minlength="2"
                           class="search-input"
                           autocomplete="off">
                    <label for="search-query" class="sr-only">Поисковый запрос</label>
                </div>
                <button type="submit" class="btn btn-primary">Найти</button>
            </form>
        </div>

        <?php if (!empty($query)): ?>
            <div class="results-section">
                <h2>Результаты поиска для "<?= esc($query) ?>"</h2>

                <?php if (empty($users)): ?>
                    <div class="no-results">
                        <i class="fas fa-user-slash"></i>
                        <p>Пользователи не найдены</p>
                        <p class="small">Попробуйте изменить поисковый запрос</p>
                    </div>
                <?php else: ?>
                    <div class="users-grid">
                        <?php foreach ($users as $user): ?>
                            <div class="user-card <?= $user['is_active'] ? 'active' : 'inactive' ?>">
                                <div class="user-avatar">
                                    <?php
                                    // Берём первые две буквы из display_name или username
                                    $name = $user['display_name'] ?? $user['username'];
                                    $initials = strtoupper(mb_substr($name, 0, 2, 'UTF-8'));
                                    ?>
                                    <span class="avatar-initials"><?= $initials ?></span>
                                </div>

                                <div class="user-info">
                                    <div class="user-name">
                                        <strong><?= esc($user['display_name'] ?? $user['username']) ?></strong>
                                        <?php if (!$user['is_active']): ?>
                                            <span class="inactive-badge">неактивен</span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="user-detail">
                                        <i class="fas fa-at"></i> <?= esc($user['username']) ?>
                                    </div>
                                    <div class="user-detail">
                                        <i class="fas fa-envelope"></i> <?= esc($user['email']) ?>
                                    </div>
                                </div>

                                <div class="user-actions">
                                    <?php if ($user['is_contact'] ?? false): ?>
                                        <span class="btn btn-success btn-disabled">
                                            <i class="fas fa-check"></i> В контактах
                                        </span>
                                    <?php elseif ($user['is_active']): ?>
                                        <form action="<?= base_url('contacts/add') ?>" method="POST">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="contact_id" value="<?= $user['id'] ?>">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-user-plus"></i> Добавить
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <span class="btn btn-secondary btn-disabled">
                                            <i class="fas fa-ban"></i> Недоступен
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
<?= $this->endSection() ?>