<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Настройки профиля<?= $this->endSection() ?>

<?= $this->section('styles') ?>
    <link rel="stylesheet" href="<?= base_url('css/profile.css') ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php
/**
 * @var array $user Массив с данными пользователя
 */
?>
    <div class="profile-container">
        <div class="profile-header">
            <h1>Настройки профиля</h1>
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

        <div class="profile-info">
            <div class="info-group">
                <label for="username_field">Логин (нельзя изменить)</label>
                <input type="text" id="username_field" value="<?= esc($user['username']) ?>" disabled>
            </div>

            <div class="info-group">
                <label for="email_field">Email (нельзя изменить)</label>
                <input type="email" id="email_field" value="<?= esc($user['email']) ?>" disabled>
            </div>
        </div>

        <div class="profile-form">
            <h2>Изменить отображаемое имя</h2>
            <form action="<?= base_url('dashboard/profile/update-name') ?>" method="POST">
                <?= csrf_field() ?>

                <div class="form-group">
                    <label for="display_name">Отображаемое имя</label>
                    <input type="text"
                           id="display_name"
                           name="display_name"
                           value="<?= old('display_name', esc($user['display_name'] ?? $user['username'])) ?>"
                           required
                           minlength="2"
                           maxlength="100"
                           placeholder="Как вас будут видеть в чатах">
                </div>

                <button type="submit" class="btn btn-primary">
                    Сохранить имя
                </button>
            </form>
        </div>
    </div>
<?= $this->endSection() ?>