document.addEventListener('DOMContentLoaded', async () => {
    // Получаем данные из data-атрибутов
    const chatContainer = document.getElementById('chatMessages');
    const userId = chatContainer.dataset.userId;
    const contactId = chatContainer.dataset.contactId;
    const contactName = chatContainer.dataset.contactName;

    if (!userId || !contactId) {
        console.error('Missing user or contact ID');
        return;
    }

    // Создаём poller
    const poller = new ChatPoller(parseInt(userId), parseInt(contactId), {
        onStatusChange: (status, message) => {
            console.log('Chat status:', status, message);
        }
    });

    // Создаём UI
    const ui = new ChatUI('chatMessages', poller);

    // Настраиваем форму отправки
    const form = document.getElementById('sendMessageForm');
    const input = document.getElementById('messageInput');
    const sendBtn = document.getElementById('sendBtn');

    form.addEventListener('submit', async (e) => {
        e.preventDefault();

        const text = input.value.trim();
        if (!text) return;

        input.disabled = true;
        sendBtn.disabled = true;

        try {
            const result = await poller.sendMessage(text);

            // Добавляем сообщение в UI сразу (оптимистичная отрисовка)
            ui.addMessage({
                id: result.data.message_id,
                sender_id: userId,
                recipient_id: contactId,
                message: text,
                is_delivered: false,
                is_read: false,
                created_at: result.data.sent_at
            }, false);

            input.value = '';
            input.style.height = 'auto';

        } catch (error) {
            alert('Ошибка: ' + error.message);
        } finally {
            input.disabled = false;
            sendBtn.disabled = false;
            input.focus();
        }
    });

    // Авто-увеличение textarea
    input.addEventListener('input', function() {
        this.style.height = 'auto';
        this.style.height = (this.scrollHeight) + 'px';
    });

    // Enter для отправки (Shift+Enter для новой строки)
    input.addEventListener('keydown', (e) => {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            form.dispatchEvent(new Event('submit'));
        }
    });
});