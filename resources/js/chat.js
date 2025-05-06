import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: process.env.MIX_PUSHER_APP_KEY,
    cluster: process.env.MIX_PUSHER_APP_CLUSTER,
    forceTLS: true,
    encrypted: true,
    auth: {
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    }
});

class Chat {
    constructor() {
        this.$messageForm = $('#message-form');
        this.$messageInput = $('#message-input');
        this.$messagesContainer = $('.chat-messages');
        this.userId = $('meta[name="user-id"]').attr('content');
        this.userType = $('meta[name="user-type"]').attr('content');
        this.otherUserId = $('meta[name="other-user-id"]').attr('content');
        this.isScrolledToBottom = true;
        this.typingTimeout = null;
        this.isTyping = false;

        this.initializeEventListeners();
        this.initializeEcho();
        this.scrollToBottom();
        this.markMessagesAsRead();
    }

    initializeEventListeners() {
        if (this.$messageForm.length) {
            this.$messageForm.on('submit', (e) => this.handleSubmit(e));

            // Track scroll position
            this.$messagesContainer.on('scroll', () => {
                const scrollHeight = this.$messagesContainer[0].scrollHeight;
                const scrollTop = this.$messagesContainer.scrollTop();
                const clientHeight = this.$messagesContainer[0].clientHeight;

                this.isScrolledToBottom = (scrollHeight - scrollTop - clientHeight) < 100;
            });

            // Handle input events for typing indicator
            this.$messageInput.on('input', () => this.handleTyping());

            // Handle input keypress for better UX
            this.$messageInput.on('keypress', (e) => {
                if (e.which === 13 && !e.shiftKey) {
                    e.preventDefault();
                    this.$messageForm.submit();
                }
            });

            // Handle window focus for marking messages as read
            $(window).on('focus', () => this.markMessagesAsRead());
        }
    }

    initializeEcho() {
        // Listen for new messages
        window.Echo.private(`chat.${this.userId}`)
            .listen('NewMessage', (e) => {
                console.log('New message received:', e);
                this.handleNewMessage(e.message);
            });

        // Join presence channel for typing indicators
        this.presenceChannel = window.Echo.join(`chat.${this.userId}.${this.otherUserId}`)
            .here((users) => {
                console.log('Users in chat:', users);
            })
            .joining((user) => {
                this.showStatusMessage(`${user.name} joined the chat`);
            })
            .leaving((user) => {
                this.showStatusMessage(`${user.name} left the chat`);
            })
            .listenForWhisper('typing', (e) => {
                this.showTypingIndicator(e.user);
            })
            .listenForWhisper('stopped-typing', () => {
                this.hideTypingIndicator();
            });
    }

    handleNewMessage(message) {
        // Remove any temporary sending message with the same content
        $('.message.sending').remove();

        // Append the new message
        this.appendMessage(message, message.sender_id === this.userId);

        // Play notification sound if message is from other user
        if (message.sender_id !== this.userId) {
            this.playNotificationSound();
        }

        // Scroll to bottom if already at bottom
        if (this.isScrolledToBottom) {
            this.scrollToBottom();
        } else {
            this.showNewMessageIndicator();
        }

        // Mark message as read if window is focused
        if (document.hasFocus()) {
            this.markMessagesAsRead();
        }
    }

    handleSubmit(e) {
        e.preventDefault();
        const message = this.$messageInput.val().trim();
        if (!message) return;

        const url = this.$messageForm.attr('action');

        // Show temporary message
        const tempMessage = {
            message: message,
            created_at: new Date(),
            sender: {
                id: this.userId,
                name: $('meta[name="user-name"]').attr('content')
            }
        };
        this.appendMessage(tempMessage, true, true);

        // Scroll to bottom
        this.scrollToBottom();

        // Clear input and disable it temporarily
        this.$messageInput.val('').prop('disabled', true);

        // Send message via AJAX
        $.ajax({
            url: url,
            method: 'POST',
            data: {
                message: message,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: (response) => {
                console.log('Message sent successfully:', response);
                if (response.status === 'success') {
                    // Remove temporary message
                    $('.message.sending').remove();

                    // Append actual message
                    this.appendMessage(response.message, true);

                    // Reset typing state
                    this.isTyping = false;
                    this.presenceChannel.whisper('stopped-typing', {});
                }
            },
            error: (error) => {
                console.error('Error sending message:', error);
                $('.message.sending').remove();
                this.showErrorMessage('Failed to send message. Please try again.');
            },
            complete: () => {
                this.$messageInput.prop('disabled', false).focus();
            }
        });
    }

    appendMessage(message, isSent, isSending = false) {
        const $messageDiv = $('<div>').addClass(`message ${isSent ? 'sent' : 'received'} mb-3`);
        if (isSending) {
            $messageDiv.addClass('sending opacity-70');
        }

        const time = new Date(message.created_at).toLocaleTimeString([], {
            hour: '2-digit',
            minute: '2-digit'
        });

        const senderRole = this.userType.includes('Admin') ?
            (isSent ? 'admin' : 'intern') :
            (isSent ? 'intern' : 'admin');

        const $messageContent = $('<div>').addClass('message-content').html(`
            <div class="message-sender">
                <div class="flex items-center">
                    <span class="mr-2">${message.sender.name}</span>
                    <span class="sender-role ${senderRole}">${senderRole.charAt(0).toUpperCase() + senderRole.slice(1)}</span>
                </div>
            </div>
            <p class="mb-1">${this.escapeHtml(message.message)}</p>
            <div class="message-time">
                <span>${time}</span>
                ${isSent ? (isSending ?
                '<i class="fas fa-clock ms-1"></i>' :
                '<i class="fas fa-check ms-1"></i>'
            ) : ''}
            </div>
        `);

        $messageDiv.append($messageContent);
        this.$messagesContainer.append($messageDiv);
    }

    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    showStatusMessage(message) {
        const $status = $('<div>')
            .addClass('status-message text-center my-2')
            .html(`<small class="text-muted">${message}</small>`);

        this.$messagesContainer.append($status);
        if (this.isScrolledToBottom) {
            this.scrollToBottom();
        }

        setTimeout(() => {
            $status.fadeOut(300, function () {
                $(this).remove();
            });
        }, 5000);
    }

    showErrorMessage(message) {
        const $error = $('<div>')
            .addClass('alert alert-danger mb-3')
            .text(message);

        this.$messageForm.before($error);

        setTimeout(() => {
            $error.fadeOut(300, function () {
                $(this).remove();
            });
        }, 5000);
    }

    showNewMessageIndicator() {
        if (!$('.new-message-indicator').length) {
            const $indicator = $('<div>')
                .addClass('new-message-indicator')
                .html(`
                    <i class="fas fa-arrow-down"></i>
                    <span>New message</span>
                `)
                .on('click', () => {
                    this.scrollToBottom();
                    $indicator.remove();
                });

            $('body').append($indicator);
        }
    }

    scrollToBottom() {
        this.$messagesContainer.scrollTop(this.$messagesContainer[0].scrollHeight);
        $('.new-message-indicator').remove();
    }

    handleTyping() {
        if (!this.isTyping) {
            this.isTyping = true;
            this.presenceChannel.whisper('typing', {
                user: {
                    name: $('meta[name="user-name"]').attr('content')
                }
            });
        }

        clearTimeout(this.typingTimeout);
        this.typingTimeout = setTimeout(() => {
            this.isTyping = false;
            this.presenceChannel.whisper('stopped-typing', {});
        }, 1000);
    }

    showTypingIndicator(user) {
        if (!$('.typing-indicator').length) {
            const $typing = $('<div>')
                .addClass('typing-indicator message received mb-3')
                .html(`
                    <div class="message-content">
                        <small class="text-muted">
                            ${user.name} is typing
                            <span class="typing-dots">
                                <span>.</span>
                                <span>.</span>
                                <span>.</span>
                            </span>
                        </small>
                    </div>
                `);

            this.$messagesContainer.append($typing);
            if (this.isScrolledToBottom) {
                this.scrollToBottom();
            }
        }
    }

    hideTypingIndicator() {
        $('.typing-indicator').fadeOut(300, function () {
            $(this).remove();
        });
    }

    markMessagesAsRead() {
        $.post('/messages/mark-as-read');
    }

    playNotificationSound() {
        // Create and play notification sound
        const audio = new Audio('/notification.mp3');
        audio.play().catch(error => {
            console.log('Error playing notification sound:', error);
        });
    }
}

// Initialize chat when document is ready
$(document).ready(() => {
    if ($('.chat-container').length) {
        new Chat();
    }
});

export default Chat;
