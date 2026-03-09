document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('loginForm');
    const login = document.getElementById('login');
    const password = document.getElementById('password');
    const submitBtn = document.getElementById('submitBtn');

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

    // Валидация логина
    login.addEventListener('input', function() {
        const value = this.value.trim();

        if (value.length === 0) {
            this.classList.remove('input-valid', 'input-invalid');
            const errorEl = document.getElementById('loginError');
            if (errorEl) errorEl.classList.remove('show');
            return;
        }

        if (value.length < 3) {
            showError(this, 'Минимум 3 символа');
        } else {
            hideError(this);
        }
    });

    // Валидация пароля
    password.addEventListener('input', function() {
        const value = this.value;

        if (value.length === 0) {
            this.classList.remove('input-valid', 'input-invalid');
            const errorEl = document.getElementById('passwordError');
            if (errorEl) errorEl.classList.remove('show');
            return;
        }

        if (value.length < 8) {
            showError(this, 'Минимум 8 символов');
        } else {
            hideError(this);
        }
    });

    // Анимация кнопки при отправке
    form.addEventListener('submit', function() {
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="loading-spinner"></span> Вход...';
    });

    // Убираем предупреждения при фокусе
    login.addEventListener('focus', function() {
        this.classList.remove('input-invalid');
    });

    password.addEventListener('focus', function() {
        this.classList.remove('input-invalid');
    });
});