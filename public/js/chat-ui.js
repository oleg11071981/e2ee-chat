/**
 * UI для чата
 */
class ChatUI {
    constructor(containerId, poller) {
        this.container = document.getElementById(containerId);
        this.poller = poller;
        this.messageTemplate = null;
        this.init();
    }

    init() {
        // Настраиваем callback для новых сообщений
        this.poller.onMessageCallback = (messages) => {
            messages.forEach(msg => this.addMessage(msg, false));
        };

        // Загружаем историю
        this.loadHistory();
    }

    async loadHistory() {
        try {
            const data = await this.poller.loadHistory(50);
            data.messages.forEach(msg => this.addMessage(msg, true));
            this.scrollToBottom();
        } catch (error) {
            console.error('Failed to load history:', error);
        }
    }

    addMessage(message, isHistory = false) {
        const messageDiv = document.createElement('div');
        messageDiv.className = `message ${message.sender_id === this.poller.userId ? 'sent' : 'received'}`;
        messageDiv.dataset.messageId = message.id;

        const time = new Date(message.created_at).toLocaleTimeString([], {
            hour: '2-digit',
            minute: '2-digit'
        });

        messageDiv.innerHTML = `
            <div class="message-content">
                <div class="message-text">${this.escapeHtml(message.message)}</div>
                <div class="message-meta">
                    <span class="message-time">${time}</span>
                    ${message.sender_id === this.poller.userId ? `
                        <span class="message-status">
                            ${message.is_read ? '✓✓' : message.is_delivered ? '✓' : '🕐'}
                        </span>
                    ` : ''}
                </div>
            </div>
        `;

        this.container.appendChild(messageDiv);

        if (!isHistory) {
            this.scrollToBottom();

            // Отмечаем как прочитанное, если сообщение для нас
            if (message.recipient_id === this.poller.userId) {
                this.poller.markAsRead(message.id);
            }
        }
    }

    scrollToBottom() {
        this.container.scrollTop = this.container.scrollHeight;
    }

    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
}