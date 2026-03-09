document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('forgotPasswordForm');
    const email = document.getElementById('email');
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

    // Предотвращение множественной отправки
    form.addEventListener('submit', function() {
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="loading-spinner"></span> Отправка...';
    });

    // Убираем предупреждения при фокусе
    email.addEventListener('focus', function() {
        this.classList.remove('input-invalid');
    });
});