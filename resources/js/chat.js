class Chat {
    constructor() {
        this.$messageForm = $('#message-form');

        // Skip if form is already initialized by inline scripts
        if (this.$messageForm.attr('data-chat-initialized') === 'true') {
            console.log('Chat form already initialized, skipping');
            return;
        }

        this.$messageInput = $('#message-input');
        this.$messagesContainer = $('.chat-messages');
        this.userId = $('meta[name="user-id"]').attr('content');
        this.userRole = $('meta[name="user-role"]').attr('content');
        this.userType = $('meta[name="user-type"]').attr('content');
        this.otherUserId = $('meta[name="other-user-id"]').attr('content');
        this.isScrolledToBottom = true;

        console.log('Chat initialized with:', {
            userId: this.userId,
            userRole: this.userRole,
            userType: this.userType,
            otherUserId: this.otherUserId
        });

        this.initializeEventListeners();
        this.scrollToBottom();

        // Mark messages as read if we're in a chat
        if (this.otherUserId) {
            this.markMessagesAsRead();
        }

        // Mark as initialized to prevent double initialization
        this.$messageForm.attr('data-chat-initialized', 'true');
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
            },
            success: () => {
                // Update chat notification badge
                updateUnreadCount();
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
    // Initialize chat if on a chat page
    if ($('.chat-container').length) {
        new Chat();
    }

    // Initialize unread message counter for all pages
    updateUnreadCount();

    // Setup polling for unread messages every 30 seconds
    setInterval(updateUnreadCount, 30000);
});

// Function to update unread count in the navbar
function updateUnreadCount() {
    $.ajax({
        url: '/messages/unread-count',
        method: 'GET',
        success: (response) => {
            const unreadCount = response.unread_count;

            // Update the badge in the navbar
            const $chatNavLink = $('.chat-nav-badge');

            if (unreadCount > 0) {
                // If badge doesn't exist, create it
                if ($chatNavLink.length === 0) {
                    // Add badge to desktop navigation
                    $('a[href*="chat.index"]').each(function () {
                        const $link = $(this);
                        if (!$link.find('.chat-nav-badge').length) {
                            $link.append(`<span class="chat-nav-badge">${unreadCount}</span>`);
                        } else {
                            $link.find('.chat-nav-badge').text(unreadCount);
                        }
                    });
                } else {
                    // Update existing badge
                    $chatNavLink.text(unreadCount);
                }
            } else {
                // Remove badge if count is 0
                $('.chat-nav-badge').remove();
            }
        }
    });
}
