import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: process.env.MIX_PUSHER_APP_KEY,
    cluster: process.env.MIX_PUSHER_APP_CLUSTER,
    forceTLS: true
});

class Chat {
    constructor() {
        this.messageForm = document.querySelector('#message-form');
        this.messageInput = document.querySelector('#message-input');
        this.messagesContainer = document.querySelector('.chat-messages');
        this.userId = document.querySelector('meta[name="user-id"]').content;
        this.userType = document.querySelector('meta[name="user-type"]').content;

        this.initializeEventListeners();
        this.initializeEcho();
        this.scrollToBottom(); // Scroll to bottom when chat is initialized
    }

    initializeEventListeners() {
        if (this.messageForm) {
            this.messageForm.addEventListener('submit', (e) => this.handleSubmit(e));
        }
    }

    initializeEcho() {
        window.Echo.private(`chat.${this.userId}`)
            .listen('NewMessage', (e) => {
                this.appendMessage(e.message, false);
                this.scrollToBottom();
            });
    }

    handleSubmit(e) {
        e.preventDefault();
        const message = this.messageInput.value.trim();
        if (!message) return;

        const url = this.messageForm.getAttribute('action');
        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify({ message }),
        })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    this.appendMessage(data.message, true);
                    this.messageInput.value = '';
                    this.scrollToBottom();
                }
            })
            .catch(error => console.error('Error:', error));
    }

    appendMessage(message, isSent) {
        const messageDiv = document.createElement('div');
        messageDiv.className = `message ${isSent ? 'sent' : 'received'} mb-3`;

        const time = new Date(message.created_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });

        messageDiv.innerHTML = `
            <div class="message-content">
                <p class="mb-1">${message.message}</p>
                <small class="${isSent ? 'text-white-50' : 'text-muted'}">
                    ${time}
                    ${isSent ? '<i class="fas fa-check ms-1"></i>' : ''}
                </small>
            </div>
        `;

        this.messagesContainer.appendChild(messageDiv);
    }

    scrollToBottom() {
        // Use requestAnimationFrame to ensure DOM updates are complete before scrolling
        requestAnimationFrame(() => {
            this.messagesContainer.scrollTop = this.messagesContainer.scrollHeight;
        });
    }
}

document.addEventListener('DOMContentLoaded', () => {
    if (document.querySelector('.chat-messages')) {
        new Chat();
    }
});
