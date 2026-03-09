<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Профиль<?= $this->endSection() ?>

<?= $this->section('styles') ?>
    <link rel="stylesheet" href="<?= base_url('css/dashboard.css') ?>">
<?= $this->endSection() ?>

<?= $this->section('content') ?>
    <div class="dashboard-container">
        <div class="page-header">
            <h1>Редактирование профиля</h1>
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

        <div class="profile-form">
            <form action="<?= base_url('dashboard/profile/update') ?>" method="POST">
                <?= csrf_field() ?>

                <div class="form-group">
                    <label for="username">Имя пользователя</label>
                    <input type="text"
                           id="username"
                           name="username"
                           value="<?= old('username', esc($user['username'] ?? '')) ?>"
                           minlength="3">
                    <small>Оставьте пустым, если не хотите менять</small>
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email"
                           id="email"
                           name="email"
                           value="<?= old('email', esc($user['email'] ?? '')) ?>">
                    <small>Оставьте пустым, если не хотите менять</small>
                </div>

                <button type="submit" class="btn btn-primary">Сохранить изменения</button>
            </form>
        </div>

        <div class="danger-zone">
            <h2>Опасная зона</h2>
            <p>Здесь будут настройки удаления аккаунта и смены пароля</p>
            <button class="btn btn-danger" disabled>Сменить пароль</button>
            <button class="btn btn-danger" disabled>Удалить аккаунт</button>
        </div>
    </div>
<?= $this->endSection() ?>