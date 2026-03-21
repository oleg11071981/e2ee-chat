<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Чат с <?= esc($contact['display_name'] ?? $contact['username']) ?><?= $this->endSection() ?>

<?= $this->section('hide_header') ?><?= $this->endSection() ?>

<?= $this->section('styles') ?>
    <link rel="stylesheet" href="<?= base_url('css/chat.css') ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<?= $this->endSection() ?>

<?= $this->section('content') ?>
    <div class="chat-page">
        <!-- Шапка чата -->
        <div class="chat-header-fixed">
            <div class="chat-header-content">
                <a href="<?= base_url('chat') ?>" class="btn-back">
                    <i class="fas fa-arrow-left"></i>
                </a>
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
        </div>

        <!-- Область сообщений (скролл) -->
        <div class="messages-area" id="chatMessages"
             data-user-id="<?= $userId ?>"
             data-contact-id="<?= $contact['id'] ?>"
             data-contact-name="<?= esc($contact['display_name'] ?? $contact['username']) ?>">
        </div>

        <!-- Поле ввода (фиксированное) -->
        <div class="message-input-fixed">
            <form id="sendMessageForm" class="message-form">
                <div class="input-wrapper">
                <textarea
                        id="messageInput"
                        placeholder="Введите сообщение..."
                        rows="1"
                        class="message-input"
                ></textarea>
                    <button type="submit" id="sendBtn" class="send-button">
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Авто-увеличение textarea
        const textarea = document.getElementById('messageInput');
        if (textarea) {
            textarea.addEventListener('input', function() {
                this.style.height = 'auto';
                this.style.height = (this.scrollHeight) + 'px';
            });
        }

        // Отправка по Enter
        const form = document.getElementById('sendMessageForm');
        const input = document.getElementById('messageInput');
        const sendBtn = document.getElementById('sendBtn');

        if (form) {
            form.addEventListener('submit', async (e) => {
                e.preventDefault();

                const text = input.value.trim();
                if (!text) return;

                input.disabled = true;
                sendBtn.disabled = true;

                try {
                    const result = await window.chatPoller?.sendMessage(text);
                    if (result) {
                        input.value = '';
                        input.style.height = 'auto';
                    }
                } catch (error) {
                    alert('Ошибка: ' + error.message);
                } finally {
                    input.disabled = false;
                    sendBtn.disabled = false;
                    input.focus();
                }
            });

            input.addEventListener('keydown', (e) => {
                if (e.key === 'Enter' && !e.shiftKey) {
                    e.preventDefault();
                    form.dispatchEvent(new Event('submit'));
                }
            });
        }
    </script>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
    <script src="<?= base_url('js/chat.js') ?>"></script>
    <script src="<?= base_url('js/chat-ui.js') ?>"></script>
    <script src="<?= base_url('js/chat-page.js') ?>"></script>
<?= $this->endSection() ?>