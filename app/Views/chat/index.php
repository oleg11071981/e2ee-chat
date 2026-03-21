<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Чаты<?= $this->endSection() ?>

<?= $this->section('styles') ?>
    <link rel="stylesheet" href="<?= base_url('css/chat.css') ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<?= $this->endSection() ?>

<?= $this->section('content') ?>
    <div class="chat-list-page">
        <div class="chat-list-header">
            <h1>Чаты</h1>
            <a href="<?= base_url('contacts/search') ?>" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Новый чат
            </a>
        </div>

        <div class="chat-list" id="chatList">
            <div class="loading">Загрузка...</div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', async function() {
            const chatList = document.getElementById('chatList');

            try {
                const response = await fetch('/contacts/get-for-chat', {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });

                const data = await response.json();

                if (data.success) {
                    if (data.contacts.length === 0) {
                        chatList.innerHTML = `
                    <div class="empty-state">
                        <i class="fas fa-comments"></i>
                        <p>У вас пока нет чатов</p>
                        <a href="<?= base_url('contacts/search') ?>" class="btn btn-primary">
                            Найти пользователей
                        </a>
                    </div>
                `;
                    } else {
                        chatList.innerHTML = data.contacts.map(contact => `
                    <a href="/chat/${contact.id}" class="chat-item">
                        <div class="chat-avatar">
                            <span class="avatar-initials">${getInitials(contact.display_name || contact.username)}</span>
                        </div>
                        <div class="chat-info">
                            <div class="chat-name">
                                <span class="name">${escapeHtml(contact.display_name || contact.username)}</span>
                                <span class="time">—</span>
                            </div>
                            <div class="chat-preview">
                                <span class="preview-text">Нажмите для начала диалога</span>
                            </div>
                        </div>
                    </a>
                `).join('');
                    }
                }
            } catch (error) {
                chatList.innerHTML = '<div class="error">Ошибка загрузки</div>';
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