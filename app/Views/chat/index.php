<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Чаты<?= $this->endSection() ?>

<?= $this->section('styles') ?>
    <link rel="stylesheet" href="<?= base_url('css/chat.css') ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<?= $this->endSection() ?>

<?= $this->section('content') ?>
    <div class="chat-container">
        <div class="chat-sidebar">
            <div class="chat-sidebar-header">
                <h2>Контакты</h2>
                <a href="<?= base_url('contacts/search') ?>" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> Добавить
                </a>
            </div>

            <div class="contacts-list" id="contactsList">
                <div class="loading">Загрузка контактов...</div>
            </div>
        </div>

        <div class="chat-main">
            <div class="welcome-screen" id="welcomeScreen">
                <div class="welcome-icon">
                    <i class="fas fa-comments"></i>
                </div>
                <h2>Добро пожаловать в чат!</h2>
                <p>Выберите контакт из списка слева, чтобы начать общение</p>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', async function() {
            const contactsList = document.getElementById('contactsList');

            try {
                const response = await fetch('/contacts/get-for-chat', {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                const data = await response.json();

                if (data.success) {
                    if (data.contacts.length === 0) {
                        contactsList.innerHTML = `
                    <div class="no-contacts">
                        <i class="fas fa-address-book"></i>
                        <p>У вас пока нет контактов</p>
                        <a href="<?= base_url('contacts/search') ?>" class="btn btn-primary btn-sm">
                            Найти пользователей
                        </a>
                    </div>
                `;
                    } else {
                        contactsList.innerHTML = data.contacts.map(contact => `
                    <a href="/chat/${contact.id}" class="contact-item ${contact.is_active ? 'active' : ''}">
                        <div class="contact-avatar">
                            <span class="avatar-initials">${getInitials(contact.display_name || contact.username)}</span>
                        </div>
                        <div class="contact-info">
                            <div class="contact-name">
                                <span class="name">${escapeHtml(contact.display_name || contact.username)}</span>
                                ${contact.is_active ? '<span class="online-indicator" title="Онлайн"></span>' : ''}
                            </div>
                            <div class="contact-username">@${escapeHtml(contact.username)}</div>
                        </div>
                        <div class="contact-status">${contact.is_active ? 'Онлайн' : 'Офлайн'}</div>
                    </a>
                `).join('');
                    }
                }
            } catch (error) {
                contactsList.innerHTML = '<div class="error">Ошибка загрузки контактов</div>';
                console.error(error);
            }
        });

        function getInitials(name) {
            return name.slice(0, 2).toUpperCase();
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
    </script>
<?= $this->endSection() ?>