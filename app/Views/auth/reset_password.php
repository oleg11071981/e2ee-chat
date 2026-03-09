<?= $this->extend('layouts/auth') ?>

<?= $this->section('title') ?>Новый пароль<?= $this->endSection() ?>

<?= $this->section('styles') ?>
    <link rel="stylesheet" href="<?= base_url('css/auth.css') ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<?= $this->endSection() ?>

<?= $this->section('content') ?>
    <div class="auth-container">
        <div class="auth-box">
            <h1>🔐 Новый пароль</h1>
            <p class="auth-subtitle">Введите новый пароль для вашего аккаунта</p>

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

            <form action="<?= base_url('reset-password/' . ($token ?? '')) ?>" method="POST" id="resetPasswordForm">
                <?= csrf_field() ?>

                <div class="form-group with-icon">
                    <label for="password">Новый пароль</label>
                    <div class="input-wrapper">
                        <i class="fas fa-lock"></i>
                        <input type="password"
                               id="password"
                               name="password"
                               required
                               minlength="8"
                               placeholder="••••••••">
                    </div>
                    <small>Минимум 8 символов</small>
                    <div class="password-strength" id="passwordStrength"></div>
                    <div class="error-message" id="passwordError"></div>
                </div>

                <div class="form-group with-icon">
                    <label for="confirm_password">Подтверждение пароля</label>
                    <div class="input-wrapper">
                        <i class="fas fa-lock"></i>
                        <input type="password"
                               id="confirm_password"
                               name="confirm_password"
                               required
                               placeholder="••••••••">
                    </div>
                    <div class="error-message" id="confirmError"></div>
                </div>

                <button type="submit" class="btn btn-primary" id="submitBtn">
                    Сохранить новый пароль
                </button>
            </form>

            <div class="auth-links">
                <div class="auth-link-row">
                    <a href="<?= base_url('login') ?>" class="auth-link">Вспомнили пароль? Войти</a>
                </div>
            </div>
        </div>
    </div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
    <script src="<?= base_url('js/auth/reset-password.js') ?>"></script>
<?= $this->endSection() ?>