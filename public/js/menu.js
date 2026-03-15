document.addEventListener('DOMContentLoaded', function() {
    const burgerMenu = document.getElementById('burgerMenu');
    const mainNav = document.getElementById('mainNav');
    const body = document.body;

    if (burgerMenu && mainNav) {
        burgerMenu.addEventListener('click', function() {
            this.classList.toggle('active');
            mainNav.classList.toggle('active');
            body.classList.toggle('menu-open');

            // Блокируем скролл при открытом меню
            if (body.classList.contains('menu-open')) {
                body.style.overflow = 'hidden';
            } else {
                body.style.overflow = '';
            }
        });

        // Закрываем меню при клике на ссылку
        mainNav.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', () => {
                burgerMenu.classList.remove('active');
                mainNav.classList.remove('active');
                body.classList.remove('menu-open');
                body.style.overflow = '';
            });
        });

        // Закрываем меню при клике вне его
        document.addEventListener('click', function(event) {
            if (body.classList.contains('menu-open') &&
                !mainNav.contains(event.target) &&
                !burgerMenu.contains(event.target)) {
                burgerMenu.classList.remove('active');
                mainNav.classList.remove('active');
                body.classList.remove('menu-open');
                body.style.overflow = '';
            }
        });

        // Закрываем меню при изменении размера окна (если стали десктопом)
        window.addEventListener('resize', function() {
            if (window.innerWidth > 768 && body.classList.contains('menu-open')) {
                burgerMenu.classList.remove('active');
                mainNav.classList.remove('active');
                body.classList.remove('menu-open');
                body.style.overflow = '';
            }
        });
    }
});