<?= $this->extend('layouts/auth') ?>

<?= $this->section('title') ?>Вход в систему<?= $this->endSection() ?>

<?= $this->section('styles') ?>
    <link rel="stylesheet" href="<?= base_url('css/auth.css') ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<?= $this->endSection() ?>

<?= $this->section('content') ?>
    <div class="auth-container">
        <div class="auth-box">
            <h1>🔐 Добро пожаловать!</h1>
            <p class="auth-subtitle">Войдите в свой аккаунт</p>

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

            <?php if (session()->has('warning')): ?>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    <p><?= session('warning') ?></p>
                </div>
            <?php endif; ?>

            <form action="<?= base_url('login') ?>" method="POST" id="loginForm">
                <?= csrf_field() ?>

                <div class="form-group with-icon">
                    <label for="login">Email или имя пользователя</label>
                    <div class="input-wrapper">
                        <i class="fas fa-user"></i>
                        <input type="text"
                               id="login"
                               name="login"
                               value="<?= old('login') ?>"
                               required
                               placeholder="your@email.com или username">
                    </div>
                    <div class="error-message" id="loginError"></div>
                </div>

                <div class="form-group with-icon">
                    <label for="password">Пароль</label>
                    <div class="input-wrapper">
                        <i class="fas fa-lock"></i>
                        <input type="password"
                               id="password"
                               name="password"
                               required
                               placeholder="••••••••">
                    </div>
                    <div class="error-message" id="passwordError"></div>
                </div>

                <div class="checkbox-group">
                    <label class="checkbox-label">
                        <input type="checkbox" name="remember" value="1">
                        <span>Запомнить меня</span>
                    </label>
                </div>

                <button type="submit" class="btn btn-primary" id="submitBtn">
                    <span>Войти</span>
                </button>
            </form>

            <div class="auth-links">
                <div class="auth-link-row">
                    <a href="<?= base_url('register') ?>" class="auth-link">Создать аккаунт</a>
                    <a href="<?= base_url('forgot-password') ?>" class="auth-link">Забыли пароль?</a>
                </div>
            </div>
        </div>
    </div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
    <script src="<?= base_url('js/auth/login.js') ?>"></script>
<?= $this->endSection() ?>