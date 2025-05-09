@extends('layouts.admin')

@section('content')
    <link rel="stylesheet" href="{{ asset('css/chat.css') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <div class="py-3 sm:py-6">
        <div class="chat-container">
            <div class="chat-card">
                <div class="chat-header">
                    <a href="{{ route('admin.chat.index') }}" class="back-button">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                    </a>
                    <div class="flex items-center ml-3 sm:ml-4">
                        <div class="avatar inline-flex items-center justify-center" style="background-color: #4f46e5;">
                            <span class="text-white">{{ substr($otherUser->name, 0, 1) }}</span>
                        </div>
                        <div class="ml-2 sm:ml-3">
                            <div class="flex items-center">
                                <h2
                                    class="text-base sm:text-lg font-semibold text-gray-900 truncate max-w-[150px] sm:max-w-none">
                                    {{ $otherUser->name }}</h2>
                                <span class="role-badge intern">Intern</span>
                            </div>
                            <p class="text-xs sm:text-sm text-gray-500">Online</p>
                        </div>
                    </div>
                </div>

                <div class="chat-messages" id="chat-messages">
                    @php
                        $currentDate = null;
                    @endphp
                    @foreach ($messages as $message)
                        @php
                            $messageDate = $message->created_at->format('Y-m-d');
                            $displayDate = $message->created_at->calendar(null, [
                                'sameDay' => '[Today]',
                                'lastDay' => '[Yesterday]',
                                'lastWeek' => '[Last] l',
                                'sameElse' => 'F j, Y',
                            ]);
                        @endphp

                        @if ($currentDate !== $messageDate)
                            <div class="date-divider">{{ $displayDate }}</div>
                            @php
                                $currentDate = $messageDate;
                            @endphp
                        @endif

                        <div class="message {{ $message->sender_id === auth()->id() ? 'sent' : 'received' }}"
                            data-message-id="{{ $message->id }}">
                            <div class="message-content">
                                <p class="mb-1 break-words">{{ $message->message }}</p>
                                <div class="message-time">
                                    <span>{{ $message->created_at->format('g:i A') }}</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="chat-input">
                    <form id="message-form" class="flex w-full">
                        @csrf
                        <input type="text" id="message-input" name="message" placeholder="Type a message..." required
                            class="focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <button type="submit" class="send-button">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 19l9 2-9-18-9 18l9-2zm0 0v-8" />
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            $(document).ready(function() {
                // Mark the form to prevent global chat.js handling
                $('#message-form').attr('data-chat-initialized', 'true');

                const $messagesContainer = $('#chat-messages');
                const $messageForm = $('#message-form');
                const $messageInput = $('#message-input');
                const userId = {{ Auth::id() }};
                const otherUserId = {{ $otherUser->id }};

                // Scroll to bottom of messages
                function scrollToBottom() {
                    $messagesContainer.scrollTop($messagesContainer[0].scrollHeight);
                }
                scrollToBottom();

                // Add new message to the chat
                function appendMessage(message, isSender = true) {
                    const messageHtml = `
                        <div class="message ${isSender ? 'sent' : 'received'}" data-message-id="${message.id}">
                            <div class="message-content">
                                <p class="mb-1 break-words">${$('<div>').text(message.message).html()}</p>
                                <div class="message-time">
                                    <span>${new Date(message.created_at).toLocaleTimeString('en-US', {
                                        hour: 'numeric',
                                        minute: 'numeric',
                                        hour12: true
                                    })}</span>
                                </div>
                            </div>
                        </div>
                    `;

                    $messagesContainer.append(messageHtml);
                    scrollToBottom();
                }

                // Listen for new messages
                const channelName = `chat.${Math.min(userId, otherUserId)}.${Math.max(userId, otherUserId)}`;

                // Message queue to handle concurrent messages
                const messageQueue = new Set();
                const processedMessages = new Set();

                window.Echo.channel(channelName)
                    .listen('ChatMessageEvent', (e) => {
                        // Only handle messages from the other user
                        if (e.sender_id === userId) {
                            console.log('Ignoring own message from WebSocket:', e.id);
                            return;
                        }

                        // Check if message already exists in the DOM
                        if ($(`[data-message-id="${e.id}"]`).length > 0) {
                            console.log('Message already displayed, skipping:', e.id);
                            return;
                        }

                        // Add message with small delay to prevent race conditions
                        setTimeout(() => {
                            appendMessage({
                                id: e.id,
                                message: e.message,
                                created_at: e.created_at,
                                sender_id: e.sender_id
                            }, false);
                        }, 50);
                    });

                // Handle message submission
                let isSubmitting = false; // Flag to prevent duplicate submissions
                $messageForm.on('submit', function(e) {
                    e.preventDefault();

                    // If already submitting, prevent duplicate
                    if (isSubmitting) {
                        console.log('Message already being sent, preventing duplicate');
                        return;
                    }

                    const message = $messageInput.val().trim();
                    if (!message) return;

                    // Set submitting flag to true
                    isSubmitting = true;

                    // Disable the input and button while sending
                    $messageInput.prop('disabled', true);
                    const $submitButton = $messageForm.find('button[type="submit"]');
                    $submitButton.prop('disabled', true);

                    $.ajax({
                        url: '/admin/chat/{{ $otherUser->id }}',
                        method: 'POST',
                        data: {
                            message: message,
                            _token: $('meta[name="csrf-token"]').attr('content')
                        },
                        dataType: 'json',
                        success: function(data) {
                            console.log('Server response:', data);

                            // Clear input first
                            $messageInput.val('');

                            appendMessage({
                                id: data.id,
                                message: data.message,
                                created_at: data.created_at,
                                sender_id: userId
                            }, true);
                        },
                        error: function(xhr, status, error) {
                            console.error('Error sending message:', error);
                            // Show error message
                            const errorHtml = `
                                <div class="error-message" role="alert">
                                    <p class="font-medium">${xhr.responseJSON?.error || 'Failed to send message'}</p>
                                </div>
                            `;
                            const $error = $(errorHtml).hide();
                            $messageForm.before($error);
                            $error.fadeIn('slow');
                            setTimeout(() => $error.fadeOut('slow', function() {
                                $(this).remove();
                            }), 5000);
                        },
                        complete: function() {
                            // Re-enable the input and button
                            $messageInput.prop('disabled', false);
                            $submitButton.prop('disabled', false);
                            $messageInput.focus();
                            isSubmitting = false; // Reset submitting flag
                        }
                    });
                });
            });
        </script>
    @endpush

    <style>
        @keyframes fade-in {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade-in {
            animation: fade-in 0.3s ease-out;
        }

        /* Prevent pull-to-refresh on mobile */
        body {
            overscroll-behavior-y: contain;
        }

        /* Improve touch scrolling */
        .chat-messages {
            -webkit-overflow-scrolling: touch;
            scroll-behavior: smooth;
        }

        /* Hide scrollbar on mobile */
        @media (max-width: 640px) {
            .chat-messages::-webkit-scrollbar {
                display: none;
            }

            .chat-messages {
                -ms-overflow-style: none;
                scrollbar-width: none;
            }
        }

        /* Prevent text selection on double tap */
        .chat-header,
        .message-content {
            -webkit-touch-callout: none;
            -webkit-user-select: none;
            user-select: none;
        }

        /* Allow text selection in message content */
        .message-content p {
            -webkit-user-select: text;
            user-select: text;
        }

        .error-message {
            background-color: #fee2e2;
            color: #991b1b;
            padding: 0.75rem;
            margin: 0.5rem 1rem;
            border-radius: 0.375rem;
            font-size: 0.875rem;
            text-align: center;
        }
    </style>
@endsection
