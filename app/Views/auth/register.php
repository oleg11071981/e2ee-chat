<?= $this->extend('layouts/auth') ?>

<?= $this->section('title') ?>Регистрация<?= $this->endSection() ?>

<?= $this->section('styles') ?>
    <link rel="stylesheet" href="<?= base_url('css/auth.css') ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<?= $this->endSection() ?>

<?= $this->section('content') ?>
    <div class="auth-container">
        <div class="auth-box">
            <h1>📝 Создать аккаунт</h1>
            <p class="auth-subtitle">Присоединяйтесь к защищённому общению</p>

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

            <form action="<?= base_url('register') ?>" method="POST" id="registerForm">
                <?= csrf_field() ?>

                <div class="form-group with-icon">
                    <label for="username">Имя пользователя</label>
                    <div class="input-wrapper">
                        <i class="fas fa-user"></i>
                        <input type="text"
                               id="username"
                               name="username"
                               value="<?= old('username') ?>"
                               required
                               minlength="3"
                               placeholder="john_doe">
                    </div>
                    <small>Только буквы, цифры и подчёркивание, от 3 символов</small>
                    <div class="error-message" id="usernameError"></div>
                </div>

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

                <div class="form-group with-icon">
                    <label for="password">Пароль</label>
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
                    Зарегистрироваться
                </button>
            </form>

            <div class="auth-links">
                <div class="auth-link-row">
                    <a href="<?= base_url('login') ?>" class="auth-link">Уже есть аккаунт? Войти</a>
                </div>
            </div>
        </div>
    </div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
    <script src="<?= base_url('js/auth/register.js') ?>"></script>
<?= $this->endSection() ?>