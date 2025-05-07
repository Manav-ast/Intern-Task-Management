class Chat {
    constructor() {
        this.$messageForm = $('#message-form');
        this.$messageInput = $('#message-input');
        this.$messagesContainer = $('.chat-messages');
        this.userId = $('meta[name="user-id"]').attr('content');
        this.userType = $('meta[name="user-type"]').attr('content');
        this.otherUserId = $('meta[name="other-user-id"]').attr('content');
        this.isScrolledToBottom = true;

        this.initializeEventListeners();
        this.scrollToBottom();
        this.markMessagesAsRead();
    }

    initializeEventListeners() {
        this.$messageForm.on('submit', this.handleSubmit.bind(this));
        this.$messagesContainer.on('scroll', () => {
            const container = this.$messagesContainer[0];
            this.isScrolledToBottom = container.scrollHeight - container.scrollTop === container.clientHeight;
        });
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

        const $messageContent = $('<div>').addClass('message-content').text(message.message);
        const $messageTime = $('<div>').addClass('message-time text-xs text-gray-500 mt-1')
            .text(new Date(message.created_at).toLocaleTimeString());

        $messageDiv.append($messageContent, $messageTime);
        this.$messagesContainer.append($messageDiv);
    }

    showErrorMessage(message) {
        const $error = $('<div>').addClass('error-message mb-3 text-red-500 text-center').text(message);
        this.$messageForm.before($error);
        setTimeout(() => $error.remove(), 5000);
    }

    markMessagesAsRead() {
        $.ajax({
            url: `/messages/mark-as-read/${this.otherUserId}`,
            method: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content')
            }
        });
    }

    scrollToBottom() {
        this.$messagesContainer.scrollTop(this.$messagesContainer[0].scrollHeight);
        $('.new-message-indicator').remove();
    }
}

// Initialize chat when document is ready
$(document).ready(() => {
    if ($('.chat-container').length) {
        new Chat();
    }
});
