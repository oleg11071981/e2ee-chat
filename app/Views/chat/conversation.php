<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Чат с <?= esc($contact['display_name'] ?? $contact['username']) ?><?= $this->endSection() ?>

<?= $this->section('styles') ?>
    <link rel="stylesheet" href="<?= base_url('css/chat.css') ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <meta name="X-CSRF-TOKEN" content="<?= csrf_hash() ?>">
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
                <a href="<?= base_url('chat') ?>" class="back-link">
                    <i class="fas fa-arrow-left"></i> Назад к списку
                </a>
                <!-- Контакты будут загружены через JavaScript -->
            </div>
        </div>

        <div class="chat-main">
            <div class="chat-header">
                <div class="chat-contact-info">
                    <div class="chat-avatar">
                        <?php
                        $name = $contact['display_name'] ?? $contact['username'];
                        $initials = strtoupper(mb_substr($name, 0, 2, 'UTF-8'));
                        ?>
                        <span class="avatar-initials"><?= $initials ?></span>
                    </div>
                    <div class="chat-contact-details">
                        <h3><?= esc($contact['display_name'] ?? $contact['username']) ?></h3>
                        <span class="contact-status <?= $contact['is_active'] ? 'online' : 'offline' ?>">
                        <?= $contact['is_active'] ? 'Онлайн' : 'Офлайн' ?>
                    </span>
                    </div>
                </div>
            </div>

            <div class="messages-container" id="chatMessages"
                 data-user-id="<?= $userId ?>"
                 data-contact-id="<?= $contact['id'] ?>"
                 data-contact-name="<?= esc($contact['display_name'] ?? $contact['username']) ?>">
            </div>

            <div class="message-input-container">
                <form id="sendMessageForm" class="message-form">
                <textarea
                        id="messageInput"
                        placeholder="Введите сообщение..."
                        rows="1"
                        class="message-input"
                ></textarea>
                    <button type="submit" id="sendBtn" class="send-button">
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Загружаем список контактов для боковой панели
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
                    if (data.contacts.length > 0) {
                        const contactsHtml = data.contacts.map(contact => `
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

                        contactsList.insertAdjacentHTML('beforeend', contactsHtml);
                    }
                }
            } catch (error) {
                console.error('Failed to load contacts:', error);
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

<?= $this->section('scripts') ?>
    <script src="<?= base_url('js/chat.js') ?>"></script>
    <script src="<?= base_url('js/chat-ui.js') ?>"></script>
    <script src="<?= base_url('js/chat-page.js') ?>"></script>
<?= $this->endSection() ?>