document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('registerForm');
    const username = document.getElementById('username');
    const email = document.getElementById('email');
    const password = document.getElementById('password');
    const confirmPassword = document.getElementById('confirm_password');
    const submitBtn = document.getElementById('submitBtn');
    const strengthBar = document.getElementById('passwordStrength');

    // Функция показа ошибки
    function showError(element, message) {
        const errorEl = document.getElementById(element.id + 'Error');
        element.classList.add('input-invalid');
        element.classList.remove('input-valid');
        if (errorEl) {
            errorEl.textContent = message;
            errorEl.classList.add('show');
        }
    }

    // Функция скрытия ошибки
    function hideError(element) {
        const errorEl = document.getElementById(element.id + 'Error');
        element.classList.remove('input-invalid');
        element.classList.add('input-valid');
        if (errorEl) {
            errorEl.classList.remove('show');
        }
    }

    // Валидация username
    username.addEventListener('input', function() {
        const value = this.value.trim();

        if (value.length === 0) {
            this.classList.remove('input-valid', 'input-invalid');
            const errorEl = document.getElementById('usernameError');
            if (errorEl) errorEl.classList.remove('show');
            return;
        }

        if (value.length < 3) {
            showError(this, 'Минимум 3 символа');
        } else if (!/^[a-zA-Z0-9_]+$/.test(value)) {
            showError(this, 'Только буквы, цифры и подчёркивание');
        } else {
            hideError(this);
        }
    });

    // Валидация email
    email.addEventListener('input', function() {
        const value = this.value.trim();
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        if (value.length === 0) {
            this.classList.remove('input-valid', 'input-invalid');
            const errorEl = document.getElementById('emailError');
            if (errorEl) errorEl.classList.remove('show');
            return;
        }

        if (!emailRegex.test(value)) {
            showError(this, 'Некорректный email');
        } else {
            hideError(this);
        }
    });

    // Индикатор надёжности пароля
    password.addEventListener('input', function() {
        const value = this.value;
        let strength = 0;

        if (value.length >= 8) strength++;
        if (value.match(/[a-z]/)) strength++;
        if (value.match(/[A-Z]/)) strength++;
        if (value.match(/[0-9]/)) strength++;
        if (value.match(/[^a-zA-Z0-9]/)) strength++;

        // Создаём или обновляем полоску
        let bar = strengthBar.querySelector('.password-strength-bar');
        if (!bar) {
            bar = document.createElement('div');
            bar.className = 'password-strength-bar';
            strengthBar.appendChild(bar);
        }

        switch(strength) {
            case 0:
            case 1:
                bar.className = 'password-strength-bar strength-weak';
                break;
            case 2:
            case 3:
                bar.className = 'password-strength-bar strength-medium';
                break;
            case 4:
            case 5:
                bar.className = 'password-strength-bar strength-strong';
                break;
        }

        // Валидация длины
        if (value.length === 0) {
            this.classList.remove('input-valid', 'input-invalid');
            const errorEl = document.getElementById('passwordError');
            if (errorEl) errorEl.classList.remove('show');
        } else if (value.length < 8) {
            showError(this, 'Минимум 8 символов');
        } else {
            hideError(this);
        }
    });

    // Проверка совпадения паролей
    confirmPassword.addEventListener('input', function() {
        const value = this.value;

        if (value.length === 0) {
            this.classList.remove('input-valid', 'input-invalid');
            const errorEl = document.getElementById('confirmError');
            if (errorEl) errorEl.classList.remove('show');
            return;
        }

        if (value !== password.value) {
            showError(this, 'Пароли не совпадают');
        } else {
            hideError(this);
        }
    });

    // Предотвращение множественной отправки
    form.addEventListener('submit', function() {
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="loading-spinner"></span> Регистрация...';
    });

    // Убираем предупреждения при фокусе
    username.addEventListener('focus', function() {
        this.classList.remove('input-invalid');
    });

    email.addEventListener('focus', function() {
        this.classList.remove('input-invalid');
    });

    password.addEventListener('focus', function() {
        this.classList.remove('input-invalid');
    });

    confirmPassword.addEventListener('focus', function() {
        this.classList.remove('input-invalid');
    });
});