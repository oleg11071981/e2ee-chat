document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('resetPasswordForm');
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
        submitBtn.innerHTML = '<span class="loading-spinner"></span> Сохранение...';
    });

    // Убираем предупреждения при фокусе
    password.addEventListener('focus', function() {
        this.classList.remove('input-invalid');
    });

    confirmPassword.addEventListener('focus', function() {
        this.classList.remove('input-invalid');
    });
});