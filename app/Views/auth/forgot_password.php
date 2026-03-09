<?= $this->extend('layouts/auth') ?>

<?= $this->section('title') ?>Восстановление пароля<?= $this->endSection() ?>

<?= $this->section('styles') ?>
    <link rel="stylesheet" href="<?= base_url('css/auth.css') ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<?= $this->endSection() ?>

<?= $this->section('content') ?>
    <div class="auth-container">
        <div class="auth-box">
            <h1>🔐 Восстановление пароля</h1>
            <p class="auth-subtitle">Введите ваш email для получения инструкций</p>

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

            <form action="<?= base_url('forgot-password') ?>" method="POST" id="forgotPasswordForm">
                <?= csrf_field() ?>

                <div class="form-group with-icon">
                    <label for="email">Email</label>
                    <div class="input-wrapper">
                        <i class="fas fa-envelope"></i>
                        <input type="email"
                               id="email"
                               name="email"
                               value="<?= old('email') ?>"
                               required
                               placeholder="your@email.com">
                    </div>
                    <div class="error-message" id="emailError"></div>
                </div>

                <button type="submit" class="btn btn-primary" id="submitBtn">
                    Отправить инструкции
                </button>
            </form>

            <div class="auth-links">
                <div class="auth-link-row">
                    <a href="<?= base_url('login') ?>" class="auth-link">Вспомнили пароль? Войти</a>
                </div>
                <div class="auth-link-row">
                    <a href="<?= base_url('register') ?>" class="auth-link">Создать аккаунт</a>
                </div>
            </div>
        </div>
    </div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
    <script src="<?= base_url('js/auth/forgot-password.js') ?>"></script>
<?= $this->endSection() ?>